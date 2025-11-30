<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CrearRequisicion extends CreateRecord
{
    protected static string $resource = RequisicionResource::class;

    protected $isSending = false;

    protected function getHeaderActions(): array
    {
        return [
            $this->getEnviarFormAction(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getGuardarFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getGuardarFormAction(): Action
    {
        return Action::make('guardar')
            ->label('Guardar')
            ->action(function () {
                $this->isSending = false;
                $this->create();
            })
            ->color('primary');
    }

    protected function getEnviarFormAction(): Action
    {
        return Action::make('enviar')
            ->label('Enviar')
            ->action(function () {
                $this->isSending = true;
                $this->create();
            })
            ->color('success')
            ->icon('heroicon-o-paper-airplane');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_estatus'] = $this->isSending ? 2 : 1;

        return $data;
    }
}
