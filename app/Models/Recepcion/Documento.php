<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'id_requisicion',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'comentarios',
    ];

    protected static function booted()
    {
        static::created(function (Documento $documento) {
            if ($documento->tipo_documento === 'Orden de Compra' && $documento->requisicion) {
                if ($documento->requisicion->id_estatus <= 5) {
                    $documento->requisicion->update(['id_estatus' => 6]);
                }
            }
        });
    }

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }
}

