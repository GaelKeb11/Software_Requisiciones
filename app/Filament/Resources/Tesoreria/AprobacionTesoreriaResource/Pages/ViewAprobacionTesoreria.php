<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Pages;

use App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\AprobacionTesoreriaResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ViewAprobacionTesoreria extends ViewRecord
{
    protected static string $resource = AprobacionTesoreriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aprobar')
                ->label('Aprobar Cotización')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('¿Aprobar cotización?')
                ->modalDescription('La requisición pasará a estado "Aprobada" y estará lista para generar Orden de Compra.')
                ->action(function () {
                    $this->record->update(['id_estatus' => 5]); // 5 = Aprobada

                    Notification::make()
                        ->title('Requisición Aprobada')
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            Action::make('rechazar')
                ->label('Rechazar Cotización')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('¿Rechazar cotización?')
                ->modalDescription('La requisición será rechazada.')
                ->action(function () {
                    $this->record->update(['id_estatus' => 9]); // 9 = Rechazada

                    Notification::make()
                        ->title('Requisición Rechazada')
                        ->danger()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}

