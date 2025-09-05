<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;

    protected $table = 'departamentos';
    protected $primaryKey = 'id_departamento';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'responsable'
    ];
}