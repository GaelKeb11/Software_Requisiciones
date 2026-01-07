<?php

namespace App\Models\Solicitud;

use App\Models\Recepcion\Requisicion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleRequisicion extends Model
{
    protected $table = 'detalle_requisicions';
    protected $primaryKey = 'id_detalle_requisicion';

    protected $fillable = [
        'id_requisicion',
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

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }
}