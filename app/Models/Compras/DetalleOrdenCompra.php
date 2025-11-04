<?php

namespace App\Models\Compras;

use App\Models\Solicitud\DetalleRequisicion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleOrdenCompra extends Model
{
    use HasFactory;

    protected $table = 'detalle_orden_compra';
    protected $primaryKey = 'id_detalle_orden';

    protected $fillable = [
        'id_orden_compra',
        'id_detalle_requisicion',
        'precio_unitario',
        'subtotal',
    ];

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class, 'id_orden_compra', 'id_orden_compra');
    }

    public function detalleRequisicion(): BelongsTo
    {
        return $this->belongsTo(DetalleRequisicion::class, 'id_detalle_requisicion', 'id_detalle_requisicion');
    }
}
