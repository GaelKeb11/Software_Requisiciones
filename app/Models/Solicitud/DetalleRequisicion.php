<?php

namespace App\Models\Solicitud;

use App\Models\Recepcion\Clasificacion;
use App\Models\Recepcion\Requisicion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class DetalleRequisicion extends Model
{
    protected $table = 'detalle_requisicions';
    protected $primaryKey = 'id_detalle_requisicion';

    protected $fillable = [
        'id_requisicion',
        'id_clasificacion_detalle',
        'cantidad',
        'unidad_medida',
        'descripcion',
        'total'
    ];

    protected $casts = [
        'descripcion' => 'encrypted',
        'total' => 'decimal:2',
    ];

    protected $attributes = [
        'total' => 0,
    ];

    protected static function booted()
    {
        static::saving(function (DetalleRequisicion $detalle) {
            if (now()->day > 8 && (int) $detalle->id_clasificacion_detalle === 2161) {
                throw ValidationException::withMessages([
                    'id_clasificacion_detalle' => 'La clasificación 2161 (Material de limpieza) solo está disponible los primeros 8 días de cada mes.',
                ]);
            }
        });
    }

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }

    public function clasificacion(): BelongsTo
    {
        return $this->belongsTo(Clasificacion::class, 'id_clasificacion_detalle');
    }
}