<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Solicitud\DetalleRequisicion;
use App\Models\Compras\Cotizacion;
use App\Models\Usuarios\Usuario;
use Illuminate\Support\Facades\Auth;
use App\Models\Recepcion\Estatus;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;


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
    ];

    protected static function booted()
    {
        static::creating(function ($requisicion) {
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

        static::saved(function ($requisicion) {
            // The document handling logic is removed as per the edit hint.
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
