<?php

namespace App\Filament\Widgets;

use App\Models\Recepcion\Requisicion;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class TesoreriaChart extends ChartWidget
{
    protected ?string $heading = 'Requisiciones por Mes';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && ($user->rol->nombre == 'Tesorería' || $user->rol->nombre == 'Administrador');
    }

    protected function getData(): array
    {
        $yearStart = now()->startOfYear();
        $yearEnd = now()->endOfYear();

        $aprobadas = Trend::query(Requisicion::where('id_estatus', 5))
            ->between(start: $yearStart, end: $yearEnd)
            ->perMonth()
            ->count();

        $rechazadas = Trend::query(Requisicion::where('id_estatus', 9))
            ->between(start: $yearStart, end: $yearEnd)
            ->perMonth()
            ->count();

        $completadas = Trend::query(Requisicion::whereHas('estatus', fn($q) => $q->where('nombre', 'Completada')))
            ->between(start: $yearStart, end: $yearEnd)
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Aprobadas',
                    'data' => $aprobadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#10b981', // Success Green
                ],
                [
                    'label' => 'Rechazadas',
                    'data' => $rechazadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444', // Danger Red
                ],
                [
                    'label' => 'Completadas',
                    'data' => $completadas->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#3b82f6', // Primary Blue
                ],
            ],
            'labels' => $aprobadas->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

