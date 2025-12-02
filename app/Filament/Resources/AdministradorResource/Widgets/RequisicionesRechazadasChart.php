<?php

namespace App\Filament\Resources\AdministradorResource\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RequisicionesRechazadasChart extends ChartWidget
{
    protected ?string $heading = 'Requisiciones Rechazadas';

    protected ?string $pollingInterval = null;

    public ?string $filter = 'year';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Esta Semana',
            'month' => 'Este Mes',
            'year' => 'Este Año',
        ];
    }

    protected function getData(): array
    {
        // Asumimos que estatus 9 = Rechazada
        $query = Requisicion::query()->where('id_estatus', 9);

        switch ($this->filter) {
            case 'week':
                $data = Trend::query($query)
                    ->between(
                        start: now()->startOfWeek(),
                        end: now()->endOfWeek(),
                    )
                    ->perDay()
                    ->count();
                break;
            case 'month':
                $data = Trend::query($query)
                    ->between(
                        start: now()->startOfMonth(),
                        end: now()->endOfMonth(),
                    )
                    ->perDay()
                    ->count();
                break;
            case 'year':
            default:
                $data = Trend::query($query)
                    ->between(
                        start: now()->startOfYear(),
                        end: now()->endOfYear(),
                    )
                    ->perMonth()
                    ->count();
                break;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Requisiciones Rechazadas',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#ef4444', // Red
                    'borderColor' => '#ef4444',
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

