<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Recepcion\Estatus;

class EditarSolicitud extends EditRecord
{
    protected static string $resource = SolicitudResource::class;

    public $statusId = null;

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $user = Auth::user();
        $isEditable = $record->id_estatus < 2 || $user->rol->nombre === 'Administrador';

        return [
            Action::make('enviar')
                ->label('Enviar Solicitud')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Enviar Solicitud')
                ->modalDescription('¿Está seguro de enviar esta solicitud? Una vez enviada, pasará al proceso de revisión.')
                ->modalSubmitActionLabel('Sí, enviar')
                ->action(function () {
                    $this->data['id_estatus'] = 2;
                    $this->save();
                })
                ->visible($isEditable && $record->id_estatus == 1),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->visible($isEditable),
        ];
    }

    protected function getFormActions(): array
    {
        $record = $this->getRecord();
        $user = Auth::user();
        $isEditable = $record->id_estatus < 2 || $user->rol->nombre === 'Administrador';

        if (!$isEditable) {
            return [
                $this->getCancelFormAction()
                    ->label('Regresar')
                    ->icon('heroicon-o-arrow-left'),
            ];
        }

        return [
            Action::make('guardar')
                ->label('Guardar')
                ->color('primary')
                ->action(function () {
                    $this->data['id_estatus'] = 1;
                    $this->save();
                }),

            $this->getCancelFormAction(),
        ];
    }
}
