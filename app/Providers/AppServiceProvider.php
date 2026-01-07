<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Recepcion\Requisicion;
use App\Models\Recepcion\Documento;
use App\Observers\RequisicionObservador;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\Usuarios\Usuario;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use App\Listeners\RegistrarIntentoFallidoLogin;
use App\Listeners\RegistrarInicioSesion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, Usuario::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Registrar relaciones polimórficas si son necesarias
        Requisicion::resolveRelationUsing('documentos', function ($requisicionModel) {
            return;
        });
       //para que llame a la funcion de folio automatico
        Requisicion::observe(RequisicionObservador::class);

        // Registrar el listener para intentos fallidos de login
        Event::listen(
            Failed::class,
            RegistrarIntentoFallidoLogin::class
        );

        // Registrar el listener para inicios de sesión exitosos
        Event::listen(
            Login::class,
            RegistrarInicioSesion::class
        );
    }
}
