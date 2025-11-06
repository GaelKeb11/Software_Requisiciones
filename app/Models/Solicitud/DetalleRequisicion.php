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

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }
}