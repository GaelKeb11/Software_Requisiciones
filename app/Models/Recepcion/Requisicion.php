<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Solicitud\DetalleRequisicion;
use App\Models\Compras\Cotizacion;
use App\Models\Usuarios\Usuario;
use App\Notifications\NuevaRequisicionNotification;
use App\Notifications\RequisicionAsignadaNotification;
use App\Notifications\RequisicionEnviadaTesoreriaNotification;
use App\Notifications\NuevoActivoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Recepcion\Estatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\RolEnum;
use App\Mail\RequisicionCreada;


class Requisicion extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'requisiciones';
    protected $primaryKey = 'id_requisicion';
    public $timestamps = true;

    protected $fillable = [
        'folio',
        'fecha_creacion',
        'fecha_recepcion',
        'hora_recepcion',
        'concepto',
        'id_departamento',
        'id_clasificacion',
        'id_usuario',
        'id_estatus',
        'id_solicitante',
        'fecha_entrega'
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_recepcion' => 'date',
        'fecha_entrega' => 'date',
        'concepto' => 'encrypted',
    ];

    protected static function booted()
    {
        static::creating(function ($requisicion) {
            $fechaReferencia = $requisicion->fecha_recepcion ?? now();

            if ($fechaReferencia->day > 8 && (int) $requisicion->id_clasificacion === 2161) {
                throw ValidationException::withMessages([
                    'id_clasificacion' => 'La clasificación 2161 (Material de limpieza) solo se puede solicitar durante los primeros 8 días de cada mes.',
                ]);
            }

            $user = Auth::user();
            if (Auth::check()) {
                $requisicion->id_solicitante = Auth::id();
                $requisicion->id_departamento = $user->id_departamento;
                if (!isset($requisicion->id_estatus)) {
                    $requisicion->id_estatus = 2;
                }
            } else {
                throw new \Exception('Usuario no autenticado');
            }
            
        });

        static::updating(function (Requisicion $requisicion) {
            $esMaterialLimpieza = (int) $requisicion->id_clasificacion === 2161;
            $fechaReferencia = $requisicion->fecha_recepcion ?? now();

            if (
                $fechaReferencia->day > 8 &&
                $esMaterialLimpieza &&
                ($requisicion->isDirty('id_clasificacion') || $requisicion->isDirty('id_estatus'))
            ) {
                throw ValidationException::withMessages([
                    'id_clasificacion' => 'La clasificación 2161 (Material de limpieza) solo se puede solicitar y enviar durante los primeros 8 días de cada mes.',
                ]);
            }
        });

        static::created(function (Requisicion $requisicion) {
            // Notificar a secretarias (recepcionistas) y gestores de compras de nueva requisición
            $destinatarios = Usuario::query()
                ->whereHas('rol', function ($q) {
                    $q->whereIn('nombre', [
                        RolEnum::RECEPCIONISTA->value,
                        RolEnum::GESTOR_ADMINISTRACION->value,
                        'Gestor de Compras', // compatibilidad
                    ]);
                })
                ->get();

            if ($destinatarios->isNotEmpty()) {
                Notification::send($destinatarios, new NuevaRequisicionNotification($requisicion));
            }

            // Enviar correo a los destinatarios con email definido
            $emails = $destinatarios->pluck('email')->filter()->all();
            if (!empty($emails)) {
                Mail::to($emails)->send(new RequisicionCreada($requisicion));
            }
        });

        static::updated(function (Requisicion $requisicion) {
            $originalEstatus = $requisicion->getOriginal('id_estatus');
            $originalGestor = $requisicion->getOriginal('id_usuario');

            // Avisar cuando se asigna un gestor (id_usuario cambia y estatus pasa a 3)
            if (
                $requisicion->id_usuario &&
                $requisicion->id_usuario !== $originalGestor &&
                $requisicion->id_estatus == 3
            ) {
                $gestor = Usuario::find($requisicion->id_usuario);
                if ($gestor) {
                    Notification::send($gestor, new RequisicionAsignadaNotification($requisicion));
                }
            }

            // Avisar a Tesorería cuando la requisición se envía para aprobación (estatus = 4)
            if ($requisicion->id_estatus == 4 && $originalEstatus != 4) {
                $tesoreria = Usuario::query()
                    ->whereHas('rol', fn ($q) => $q->where('nombre', RolEnum::TESORERIA->value))
                    ->get();

                if ($tesoreria->isNotEmpty()) {
                    Notification::send($tesoreria, new RequisicionEnviadaTesoreriaNotification($requisicion));
                }
            }

            // Avisar a recepcionistas cuando la requisición esté en estatus 2 (Recibida)
            if ($requisicion->id_estatus == 2 && $originalEstatus != 2) {
                $recepcionistas = Usuario::query()
                    ->whereHas('rol', fn ($q) => $q->where('nombre', RolEnum::RECEPCIONISTA->value))
                    ->get();

                if ($recepcionistas->isNotEmpty()) {
                    Notification::send($recepcionistas, new NuevaRequisicionNotification($requisicion));
                }
            }

            // Marcar artículos como activos y notificar al solicitante cuando se completa (estatus 8) y clasificación >= 5000
            if (
                $requisicion->id_estatus == 8 &&
                $originalEstatus != 8 &&
                (int) $requisicion->id_clasificacion >= 5000
            ) {
                $requisicion->detalles()
                    ->where('es_activo', false)
                    ->update(['es_activo' => true]);

                if ($requisicion->solicitante) {
                    Notification::send($requisicion->solicitante, new NuevoActivoNotification($requisicion));
                }
            }
        });
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function clasificacion(): BelongsTo
    {
        return $this->belongsTo(Clasificacion::class, 'id_clasificacion');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Usuarios\Usuario::class, 'id_usuario');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_solicitante');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(Estatus::class, 'id_estatus');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_requisicion');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleRequisicion::class, 'id_requisicion');
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class, 'id_requisicion');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['estado', 'comentarios'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
