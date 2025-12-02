<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class RequisicionesTesoreriaChart extends ChartWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'Requisiciones por Mes';
    }

    protected function getData(): array
    {
        $dataAprobadas = Trend::query(Requisicion::where('id_estatus', 5))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $dataRechazadas = Trend::query(Requisicion::where('id_estatus', 6))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        $dataCompletadas = Trend::query(Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Completada')))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Aprobadas',
                    'data' => $dataAprobadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981', // success
                ],
                [
                    'label' => 'Rechazadas',
                    'data' => $dataRechazadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444', // danger
                ],
                [
                    'label' => 'Completadas',
                    'data' => $dataCompletadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6', // primary
                ],
            ],
            'labels' => $dataAprobadas->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

