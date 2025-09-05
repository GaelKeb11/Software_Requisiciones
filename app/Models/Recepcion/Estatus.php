<?php

namespace App\Models\Recepcion;

use Illuminate\Database\Eloquent\Model;

class Estatus extends Model
{
    protected $table = 'estatus';
    protected $primaryKey = 'id_estatus';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'color'
    ];
}