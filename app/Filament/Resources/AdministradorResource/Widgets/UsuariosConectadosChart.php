<?php

namespace App\Filament\Resources\AdministradorResource\Widgets;

use App\Models\HistorialLogin;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class UsuariosConectadosChart extends ChartWidget
{
    protected ?string $heading = 'Usuarios Conectados';

    protected ?string $pollingInterval = null;

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Esta Semana',
            'month' => 'Este Mes',
        ];
    }

    protected function getData(): array
    {
        $query = HistorialLogin::query();

        switch ($this->filter) {
            case 'today':
                $data = Trend::model(HistorialLogin::class)
                    ->between(
                        start: now()->startOfDay(),
                        end: now()->endOfDay(),
                    )
                    ->perHour()
                    ->count();
                break;
            case 'week':
                $data = Trend::model(HistorialLogin::class)
                    ->between(
                        start: now()->startOfWeek(),
                        end: now()->endOfWeek(),
                    )
                    ->perDay()
                    ->count();
                break;
            case 'month':
            default:
                $data = Trend::model(HistorialLogin::class)
                    ->between(
                        start: now()->startOfMonth(),
                        end: now()->endOfMonth(),
                    )
                    ->perDay()
                    ->count();
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inicios de Sesión',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#10b981',
                    'borderColor' => '#10b981',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

