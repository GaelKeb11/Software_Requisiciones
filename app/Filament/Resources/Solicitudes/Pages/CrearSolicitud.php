<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

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

    /**
     * Fuerza datos necesarios antes de crear la solicitud.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (!$user || !$user->id_departamento) {
            Notification::make()
                ->title('Departamento no asignado')
                ->body('Asigna un departamento al usuario antes de crear la solicitud.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'solicitante_display' => 'El usuario autenticado no tiene un departamento asignado.',
            ]);
        }

        $data['id_departamento'] = $user->id_departamento;
        $data['id_solicitante'] = $user->id_usuario;

        return $data;
    }
}
