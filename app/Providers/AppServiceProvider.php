<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Recepcion\Requisicion;
use App\Models\Recepcion\Documento;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar relaciones polimórficas si son necesarias
    Requisicion::resolveRelationUsing('documentos', function ($requisicionModel) {
        return $requisicionModel->hasMany(Documento::class, 'id_requisicion');
    });
    }
}
