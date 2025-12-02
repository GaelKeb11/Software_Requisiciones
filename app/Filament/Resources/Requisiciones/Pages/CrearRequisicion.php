<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

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
        /** @var \App\Models\Usuarios\Usuario|null $user */
        $user = Auth::user();

        if ($user && $user->esRecepcionista()) {
            // Recepcionista: Guardar = Recibida (2), Enviar = Asignada / En Cotización (3)
            $data['id_estatus'] = $this->isSending ? 3 : 2;
        } else {
            // Otros roles mantienen el flujo habitual (Borrador / Recibida)
            $data['id_estatus'] = $this->isSending ? 2 : 1;
        }

        return $data;
    }
}
