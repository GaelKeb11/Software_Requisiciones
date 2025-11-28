<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Solicitud\DetalleRequisicion;

class DetalleCotizacion extends Model
{
    protected $table = 'detalle_cotizacion';
    protected $primaryKey = 'id_detalle_cotizacion';

    protected $fillable = [
        'id_cotizacion',
        'id_detalle_requisicion',
        'cantidad_cotizada',
        'unidad_medida',
        'descripcion',
        'precio_unitario',
        'subtotal'
    ];

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }

    public function detalleRequisicion(): BelongsTo
    {
        return $this->belongsTo(DetalleRequisicion::class, 'id_detalle_requisicion');
    }
}

