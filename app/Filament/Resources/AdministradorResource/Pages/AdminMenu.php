<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class AdminMenu extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AdministradorResource::class;

    protected string $view = 'filament.resources.administrador-resource.pages.admin-menu';

    protected static ?string $title = 'Menú de Administración';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
        // Eliminar todo el bloque de notificaciones de respaldo
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Acciones Rápidas')
                    ->components([
                        ToggleButtons::make('menu_action')
                            ->hiddenLabel()
                            ->options([
                                'logs' => 'Logs del Sistema',
                                'estadisticas' => 'Estadísticas y Reportes',
                                'respaldos' => 'Respaldos de la Base de Datos',
                            ])
                            ->icons([
                                'logs' => 'heroicon-o-document-text',
                                'estadisticas' => 'heroicon-o-chart-pie',
                                'respaldos' => 'heroicon-o-cloud-arrow-down',
                            ])
                            ->colors([
                                'logs' => 'info',
                                'estadisticas' => 'success',
                                'respaldos' => 'warning',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                if ($state === 'logs') {
                                    return redirect()->to(AdministradorResource::getUrl('logs'));
                                }
                                if ($state === 'estadisticas') {
                                    return redirect()->to(AdministradorResource::getUrl('estadisticas'));
                                }
                                if ($state === 'respaldos') {
                                    return redirect()->to(AdministradorResource::getUrl('list'));
                                }
                            })
                    ]),
            ])
            ->statePath('data');
    }
}
