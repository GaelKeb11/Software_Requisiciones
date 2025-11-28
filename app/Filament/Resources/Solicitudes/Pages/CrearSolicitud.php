<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CrearSolicitud extends CreateRecord
{
    protected static string $resource = SolicitudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('enviar')
                ->label('Enviar Solicitud')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary')
                ->outlined()
                ->requiresConfirmation()
                ->modalHeading('Enviar Solicitud')
                ->modalDescription('¿Está seguro de enviar esta solicitud? Una vez enviada, pasará al proceso de revisión.')
                ->modalSubmitActionLabel('Sí, enviar')
                ->action(function () {
                    $this->data['id_estatus'] = 2; // 2 = Recibida/Enviada
                    $this->create();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('borrador')
                ->label('Guardar Borrador')
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->outlined()
                ->requiresConfirmation()
                ->modalHeading('Guardar Borrador')
                ->modalDescription('La solicitud se guardará como borrador y podrá editarla más tarde.')
                ->modalSubmitActionLabel('Guardar')
                ->action(function () {
                    $this->data['id_estatus'] = 1; // 1 = Borrador
        $this->create();
                }),

            $this->getCancelFormAction()
                ->label('Cancelar')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->outlined(),
        ];
    }
}
