<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'id_estatus'

    ];

    protected $casts = [
        'fecha_creacion' => 'date',
        'fecha_recepcion' => 'date',
    ];

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
        return $this->belongsTo(\App\Models\User::class, 'id_usuario');
    }

    public function estatus(): BelongsTo
    {
        return $this->belongsTo(Estatus::class, 'id_estatus');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'id_requisicion');
    }
}
