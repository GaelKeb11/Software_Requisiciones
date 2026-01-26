<?php

namespace App\Filament\Resources\Usuarios\Pages;

use App\Filament\Resources\Usuarios\UsuariosResource;
use App\Models\Recepcion\Departamento;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;


class CreateUsuarios extends CreateRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Guardar'),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
    
        return $data;
    }

    /**
     * Después de crear el usuario, si el rol es Director
     * asigna el nombre completo como responsable del departamento.
     */
    protected function afterCreate(): void
    {
        $usuario = $this->record;

        if (! $usuario || $usuario->rol?->nombre !== 'Director') {
            return;
        }

        $departamentoId = $usuario->id_departamento;
        if (! $departamentoId) {
            return;
        }

        $departamento = Departamento::find($departamentoId);
        if (! $departamento) {
            return;
        }

        $departamento->update([
            'responsable' => $usuario->nombreCompleto,
        ]);
    }
}
