<?php

namespace App\Filament\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TesoreriaStats extends BaseWidget
{
    protected static ?int $sort = 1;

    // Ocultar del Dashboard principal si se desea mostrar solo en la página de reportes
    // O permitir verlo si el usuario es Tesorería
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->rol->nombre == 'Tesorería' || $user->rol->nombre == 'Administrador');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Pendientes de Aprobación', Requisicion::where('id_estatus', 4)->count())
                ->description('Requieren revisión')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Aprobadas', Requisicion::where('id_estatus', 5)->count())
                ->description('Listas para OC')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Rechazadas', Requisicion::where('id_estatus', 9)->count())
                ->description('Devueltas')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Completadas', Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Completada'))->count())
                ->description('Proceso finalizado')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('success'), // Or primary/warning as per preference, user image used yellow icon but text looks dark. Let's use primary or custom.
        ];
    }
}

