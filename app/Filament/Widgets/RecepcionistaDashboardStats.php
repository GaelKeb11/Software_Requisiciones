<?php

namespace App\Filament\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class RecepcionistaDashboardStats extends BaseWidget
{
    public static function canView(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esRecepcionista();
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Por Recepcionar', Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Recibida'))->count())
                ->description('Requisiciones pendientes de recepción')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('warning'),

            Stat::make('Recepcionadas Hoy', Requisicion::whereDate('fecha_recepcion', today())->count())
                ->description('Requisiciones procesadas hoy')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success'),

            Stat::make('Requisiciones Recibidas', Requisicion::where('id_estatus', 2)->count())
                ->description('Total de requisiciones recibidas')
                ->icon('heroicon-o-check-badge')
                ->color('info'),
        ];
    }
}

