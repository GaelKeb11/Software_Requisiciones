<?php

namespace App\Filament\Widgets;

use App\Models\Recepcion\Requisicion;
use App\Models\Compras\OrdenCompra;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios\Usuario;


class GestorComprasDashboardStats extends BaseWidget
{
    public static function canView(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esGestorDireccionAdministracion();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Por Cotizar', Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Asignada / En Cotización'))->count())
                ->description('Requisiciones esperando cotización')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),

            Stat::make('Órdenes Generadas', OrdenCompra::whereDate('created_at', today())->count())
                ->description('Órdenes de compra creadas hoy')
                ->icon('heroicon-o-shopping-cart')
                ->color('success'),
        ];
    }
}

