<?php

namespace App\Listeners;

use App\Models\HistorialLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class RegistrarInicioSesion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        HistorialLogin::create([
            'id_usuario' => $event->user->getAuthIdentifier(),
            'ip' => Request::ip(),
        ]);
    }
}
