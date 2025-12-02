<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use App\Filament\Resources\AdministradorResource\Widgets\EstadisticasGeneral;
use App\Models\Recepcion\Requisicion;
use App\Models\Usuarios\Usuario;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Enums\ActionSize;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsuariosExport;
use App\Exports\RequisicionesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Filament\Resources\AdministradorResource\Widgets\RequisicionesTotalesChart;
use App\Filament\Resources\AdministradorResource\Widgets\UsuariosConectadosChart;
use App\Filament\Resources\AdministradorResource\Widgets\RequisicionesCompletadasChart;
use App\Filament\Resources\AdministradorResource\Widgets\RequisicionesRechazadasChart;

class EstadisticasReportes extends Page
{
    protected static string $resource = AdministradorResource::class;

    protected string $view = 'filament.resources.administrador-resource.pages.estadisticas-reportes';

    protected static ?string $title = 'Estadísticas y Reportes';
    
    protected static ?string $navigationLabel = 'Estadísticas y Reportes';
    
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected function getHeaderWidgets(): array
    {
        return [
            EstadisticasGeneral::class,
            RequisicionesTotalesChart::class,
            UsuariosConectadosChart::class,
            RequisicionesCompletadasChart::class,
            RequisicionesRechazadasChart::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ActionGroup::make([
                Action::make('usuarios_excel')
                    ->label('Usuarios Excel')
                    ->icon('heroicon-o-table-cells')
                    ->action(fn () => Excel::download(new UsuariosExport, 'usuarios.xlsx')),
                
                Action::make('usuarios_pdf')
                    ->label('Usuarios PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(fn () => $this->exportUsersPdf()),
            ])
            ->label('Exportar Usuarios')
            ->icon('heroicon-o-users')
            ->color('primary')
            ->button(),

            \Filament\Actions\ActionGroup::make([
                Action::make('requisiciones_excel')
                    ->label('Requisiciones Excel')
                    ->icon('heroicon-o-table-cells')
                    ->action(fn () => Excel::download(new RequisicionesExport, 'requisiciones.xlsx')),

                Action::make('requisiciones_pdf')
                    ->label('Requisiciones PDF')
                    ->icon('heroicon-o-document-text')
                    ->action(fn () => $this->exportRequisitionsPdf()),
            ])
            ->label('Exportar Requisiciones')
            ->icon('heroicon-o-document-duplicate')
            ->color('warning')
            ->button(),
        ];
    }

    public function exportUsersPdf()
    {
        $usuarios = Usuario::with(['rol', 'departamento'])->get();
        $pdf = Pdf::loadView('exports.usuarios-pdf', ['usuarios' => $usuarios]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'usuarios.pdf');
    }

    public function exportUsersExcel()
    {
        return Excel::download(new UsuariosExport, 'usuarios.xlsx');
    }

    public function exportRequisitionsPdf()
    {
        // Using the existing view structure but ensuring we pass data
        $requisiciones = Requisicion::with(['usuario', 'departamento', 'estatus', 'cotizaciones.detalles'])->get();
        $pdf = Pdf::loadView('exports.requisiciones-pdf', [
            'requisiciones' => $requisiciones,
            'startDate' => 'Inicio', // Placeholder or use filter if we implemented filtering on reports
            'endDate' => 'Fin'
        ]);
        
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'requisiciones.pdf');
    }
}

