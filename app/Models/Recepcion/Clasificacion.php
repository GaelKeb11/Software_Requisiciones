<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Clasificacion extends Model
{
    use SoftDeletes;

    protected $table = 'clasificaciones';
    protected $primaryKey = 'id_clasificacion';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}