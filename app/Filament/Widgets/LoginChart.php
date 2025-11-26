<?php

namespace App\Filament\Widgets;

use App\Models\HistorialLogin;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class LoginChart extends ChartWidget
{
    protected ?string $heading = 'Inicios de Sesión (Últimos 7 días)';

    protected function getData(): array
    {
        $data = Trend::model(HistorialLogin::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Inicios de sesión',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'backgroundColor' => '#4f46e5',
                    'borderColor' => '#4f46e5',
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
