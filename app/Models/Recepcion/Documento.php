<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Documento extends Model
{
    use SoftDeletes;

    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    public $timestamps = true;

    protected $fillable = [
        'nombre_archivo',
        'ruta_archivo',
        'tipo_documento',
        'requisicion_id'
    ];

    public function requisicion(): BelongsTo
    {
        return $this->belongsTo(Requisicion::class, 'id_requisicion');
    }

    protected static function booted()
    {
        static::deleting(function ($documento) {
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
        });
    }

    public function getRutaCompletaAttribute()
    {
        return storage_path('app/public/documentos/' . $this->nombre_archivo);
    }

    public function getUrlDescargaAttribute()
    {
        return asset('storage/documentos/' . $this->nombre_archivo);
    }
}
