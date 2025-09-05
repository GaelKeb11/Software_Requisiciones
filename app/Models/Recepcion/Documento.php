<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use SoftDeletes;

    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    public $timestamps = true;

    protected $fillable = [
        'id_requisicion',
        'tipo_documento',
        'nombre_archivo',
        'ruta_archivo',
        'comentarios'
    ];

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }
}