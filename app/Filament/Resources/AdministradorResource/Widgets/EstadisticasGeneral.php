<?php

namespace App\Filament\Resources\AdministradorResource\Widgets;

use App\Models\HistorialLogin;
use App\Models\Recepcion\Requisicion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EstadisticasGeneral extends BaseWidget
{
    protected ?string $pollingInterval = null;

    public ?string $filter = 'today';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Esta Semana',
            'month' => 'Este Mes',
            'year' => 'Este Año',
        ];
    }

    protected function getStats(): array
    {
        $startDate = now();
        $endDate = now();

        switch ($this->filter) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $label = 'hoy';
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $label = 'esta semana';
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $label = 'este mes';
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $label = 'este año';
                break;
        }

        // Logins
        // Assuming HistorialLogin has created_at
        $logins = HistorialLogin::whereBetween('created_at', [$startDate, $endDate])->count();

        // Requisiciones Solicitadas (Total created)
        $reqSolicitadas = Requisicion::whereBetween('created_at', [$startDate, $endDate])->count();

        // Requisiciones Aprobadas (Status 5)
        // Note: We filter by when they were *created* or when they were *approved*?
        // Usually "Requisiciones Aprobadas al dia" in a dashboard context often implies "Requisitions created today that are approved"
        // OR "Requisitions approved today". Without an approval log table easily accessible, filtering by created_at + status is a common proxy.
        // If we want strictly "Approved within this date range", we'd need to query activity logs or updated_at if status changed.
        // For simplicity and performance, we will use created_at + status, but ideally check updated_at if status is 5.
        // Let's use updated_at for Approved/Rejected to reflect the action time.
        
        $reqAprobadas = Requisicion::where('id_estatus', 5)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $reqRechazadas = Requisicion::where('id_estatus', 9)
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        return [
            Stat::make('Inicios de Sesión', $logins)
                ->description("Registrados {$label}")
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Requisiciones Solicitadas', $reqSolicitadas)
                ->description("Creadas {$label}")
                ->descriptionIcon('heroicon-m-document-plus')
                ->color('primary'),

            Stat::make('Requisiciones Aprobadas', $reqAprobadas)
                ->description("Aprobadas {$label}")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Requisiciones Rechazadas', $reqRechazadas)
                ->description("Rechazadas {$label}")
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}

