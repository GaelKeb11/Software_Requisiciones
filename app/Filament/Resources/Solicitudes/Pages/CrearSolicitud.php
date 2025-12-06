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
                ->label('Enviar')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action(function () {
                    $this->data['id_estatus'] = 2; // 2 = Recibida/Enviada
                    $this->create();
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('guardar')
                ->label('Guardar')
                ->color('primary')
                ->action(function () {
                    $this->data['id_estatus'] = 1; // 1 = Borrador
                    $this->create();
                }),

            $this->getCancelFormAction(),
        ];
    }
}
