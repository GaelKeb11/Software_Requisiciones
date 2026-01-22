<?php

namespace App\Models\Compras;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionAdjunto extends Model
{
    protected $table = 'cotizacion_adjuntos';
    protected $primaryKey = 'id_adjunto';

    protected $fillable = [
        'id_cotizacion',
        'nombre_archivo',
        'ruta_archivo',
        'mime_type',
        'size',
        'comentarios',
    ];

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion');
    }
}

