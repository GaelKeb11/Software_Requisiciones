<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntentoFallidoLogin extends Model
{
    protected $table = 'intentos_fallidos_logins';
    protected $fillable = ['email', 'ip', 'user_agent'];
}
