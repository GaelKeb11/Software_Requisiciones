<?php

namespace App\Filament\Resources\AdministradorResource\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class RequisicionesTotalesChart extends ChartWidget
{
    protected ?string $heading = 'Cantidad de Requisiciones Totales';
    
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
        $query = Requisicion::query();
        
        switch ($this->filter) {
            case 'week':
                $data = Trend::model(Requisicion::class)
                    ->between(
                        start: now()->startOfWeek(),
                        end: now()->endOfWeek(),
                    )
                    ->perDay()
                    ->count();
                break;
            case 'month':
                $data = Trend::model(Requisicion::class)
                    ->between(
                        start: now()->startOfMonth(),
                        end: now()->endOfMonth(),
                    )
                    ->perDay()
                    ->count();
                break;
            case 'year':
            default:
                $data = Trend::model(Requisicion::class)
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
                    'label' => 'Requisiciones Creadas',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

