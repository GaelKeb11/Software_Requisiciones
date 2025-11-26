<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialLogin extends Model
{
    protected $fillable = ['id_usuario', 'ip'];
}
