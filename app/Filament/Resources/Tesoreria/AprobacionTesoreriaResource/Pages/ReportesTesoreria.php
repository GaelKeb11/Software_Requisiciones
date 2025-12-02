<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Pages;

use App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\AprobacionTesoreriaResource;
use App\Filament\Widgets\TesoreriaStats;
use App\Filament\Widgets\TesoreriaChart;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Recepcion\Requisicion;
use Illuminate\Support\Facades\Blade;

class ReportesTesoreria extends Page
{
    protected static string $resource = AprobacionTesoreriaResource::class;

    protected string $view = 'filament.resources.tesoreria.aprobacion-tesoreria-resource.pages.reportes-tesoreria';

    protected static ?string $title = 'Reportes y Estadísticas';

    protected static ?string $navigationLabel = 'Reportes';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportar_pdf')
                ->label('Exportar PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->form([
                    Select::make('filtro_estatus')
                        ->label('Seleccionar Registros')
                        ->options([
                            'all' => 'Todas (Completadas y Rechazadas)',
                            'completadas' => 'Solo Completadas',
                            'rechazadas' => 'Solo Rechazadas',
                        ])
                        ->default('all')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $query = Requisicion::query()
                        ->with(['usuario', 'departamento', 'cotizaciones', 'estatus']);

                    $filtroTexto = '';

                    switch ($data['filtro_estatus']) {
                        case 'completadas':
                            $query->whereHas('estatus', fn($q) => $q->where('nombre', 'Completada'));
                            $filtroTexto = 'Solo Completadas';
                            break;
                        case 'rechazadas':
                            $query->where('requisiciones.id_estatus', 9); // 9 = Rechazada
                            $filtroTexto = 'Solo Rechazadas';
                            break;
                        case 'all':
                        default:
                            // Filtrar solo Completadas y Rechazadas para "Todas"
                            $query->where(function($q) {
                                $q->where('requisiciones.id_estatus', 9) // 9 = Rechazada
                                  ->orWhereHas('estatus', fn($sq) => $sq->where('nombre', 'Completada'));
                            });
                            
                            // Ordenar: Primero Completadas, luego Rechazadas
                            // join para ordenar por estatus.nombre
                            $query->join('estatus', 'requisiciones.id_estatus', '=', 'estatus.id_estatus')
                                  ->orderBy('estatus.nombre', 'asc')
                                  ->select('requisiciones.*'); 
                            
                            $filtroTexto = 'Todas (Completadas y Rechazadas)';
                            break;
                    }

                    $requisiciones = $query->get();

                    $pdf = Pdf::loadView('pdf.reporte-tesoreria', [
                        'requisiciones' => $requisiciones,
                        'filtro' => $filtroTexto
                    ]);

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, 'reporte-tesoreria-' . now()->format('Y-m-d-His') . '.pdf');
                })
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TesoreriaStats::class,
            TesoreriaChart::class,
        ];
    }
}
