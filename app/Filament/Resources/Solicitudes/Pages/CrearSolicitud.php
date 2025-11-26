<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use App\Models\Recepcion\Estatus;

class CrearSolicitud extends CreateRecord
{
    protected static string $resource = SolicitudResource::class;

    public $statusId = 1;

    protected function getFormActions(): array
    {
        $colorBorrador = Estatus::find(1)?->color ?? 'secondary';
        $colorRecibida = Estatus::find(2)?->color ?? 'primary';

        return [
            $this->getCreateFormAction()
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
        $this->create();
    }

    public function saveAndSend()
    {
        $this->statusId = 2;
        $this->create();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_estatus'] = $this->statusId;
        return $data;
    }
}
