<?php

namespace App\Listeners;

use App\Models\IntentoFallidoLogin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class RegistrarIntentoFallidoLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        IntentoFallidoLogin::create([
            'email' => $event->credentials['email'] ?? 'Desconocido',
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
