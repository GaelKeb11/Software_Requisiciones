<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TesoreriaStatsOverview extends BaseWidget
{
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

            Stat::make('Rechazadas', Requisicion::where('id_estatus', 6)->count())
                ->description('Devueltas')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Completadas', Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Completada'))->count())
                ->description('Proceso finalizado')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),
        ];
    }
}

