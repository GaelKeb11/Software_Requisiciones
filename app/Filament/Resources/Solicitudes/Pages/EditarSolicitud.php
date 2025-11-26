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

    protected function getFormActions(): array
    {
        $record = $this->getRecord();
        $user = Auth::user();

        // Bloquear edición si ya fue enviada (estatus >= 2) y el usuario es Solicitante (o no es Admin)
        // Asumimos que si no es Admin, es Solicitante y debe bloquearse.
        if ($record->id_estatus >= 2 && !$user->esAdministrador()) {
            return [
                $this->getCancelFormAction()
                    ->label('Regresar'),
            ];
        }

        $colorBorrador = Estatus::find(1)?->color ?? 'secondary';
        $colorRecibida = Estatus::find(2)?->color ?? 'primary';

        return [
            $this->getSaveFormAction()
                ->label('Guardar Borrador')
                ->action('saveDraft')
                ->color($colorBorrador),

            Action::make('enviar')
                ->label('Enviar')
                ->action('saveAndSend')
                ->color($colorRecibida),

            $this->getCancelFormAction(),
        ];
    }

    public function saveDraft()
    {
        $this->statusId = 1;
        $this->save();
    }

    public function saveAndSend()
    {
        $this->statusId = 2;
        $this->save();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->statusId) {
            $data['id_estatus'] = $this->statusId;
        }
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn ($record) => $record->id_estatus >= 2 && !Auth::user()->esAdministrador()),
        ];
    }
}
