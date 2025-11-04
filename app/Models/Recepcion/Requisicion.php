<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Solicitud\DetalleRequisicion;
use Illuminate\Support\Facades\Auth;
use App\Models\Recepcion\Estatus;

class Requisicion extends Model
{
    use SoftDeletes;

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
        'id_solicitante'
    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_recepcion' => 'date',
        
    ];

    protected static function booted()
    {
        static::creating(function ($requisicion) {
            $user = Auth::user();
            if (Auth::check()) {
                $requisicion->id_solicitante = Auth::id();
                $requisicion->id_departamento = $user->id_departamento;
                $requisicion->id_estatus = 1;
            } else {
                throw new \Exception('Usuario no autenticado');
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
}
