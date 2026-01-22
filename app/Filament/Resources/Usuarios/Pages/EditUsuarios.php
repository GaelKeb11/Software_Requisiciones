<?php

namespace App\Filament\Resources\Usuarios\Pages;

use App\Filament\Resources\Usuarios\UsuariosResource;
use App\Models\Recepcion\Departamento;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsuarios extends EditRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()
                ->label('Guardar'),
            $this->getCancelFormAction(),
        ];
    }

    protected function afterFill(): void
    {
        // Obtenemos el rol y el departamento del registro que se está editando.
        $rolNombre = $this->record->rol?->nombre;
        $deptoNombre = $this->record->departamento?->nombre;

        if (!$rolNombre) {
            return;
        }

        $grupo = match ($rolNombre) {
            'Director' => $deptoNombre === 'Dirección de Administración' ? 'DireccionAdministracion' : 'Director',
            'Recepcionista', 'Gestor de Administración', 'Gestor de Compras' => 'DireccionAdministracion',
            'Solicitante' => 'Solicitante',
            default => null,
        };

        if ($grupo) {
            // Obtenemos los datos actuales del formulario, añadimos el 'grupo_rol'
            // y volvemos a llenar para preservar los datos existentes.
            $data = $this->form->getState();
            $data['grupo_rol'] = $grupo;
            $this->form->fill($data);
        }
    }
}
