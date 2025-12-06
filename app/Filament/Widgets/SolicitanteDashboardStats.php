<?php

namespace App\Filament\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SolicitanteDashboardStats extends BaseWidget
{
    public static function canView(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esSolicitante();
    }

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            Stat::make('Mis Requisiciones', Requisicion::where('id_usuario', $userId)->count())
                ->description('Total de requisiciones creadas')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Pendientes', Requisicion::where('id_usuario', $userId)
                ->whereHas('estatus', fn($q) => $q->where('nombre', 'Pendiente'))
                ->count())
                ->description('Requisiciones en espera')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Aprobadas', Requisicion::where('id_usuario', $userId)
                ->whereHas('estatus', fn($q) => $q->where('nombre', 'Aprobada'))
                ->count())
                ->description('Requisiciones aprobadas')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}

