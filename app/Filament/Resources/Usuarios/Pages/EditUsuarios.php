<?php

namespace App\Filament\Resources\Usuarios\Pages;

use App\Filament\Resources\Usuarios\UsuariosResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUsuarios extends EditRecord
{
    protected static string $resource = UsuariosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si se proporciona una nueva contraseña, la hasheamos y la asignamos.
        if (!empty($data['new_password'])) {
            $data['password'] = bcrypt($data['new_password']);
        }

        // Eliminamos las claves de la nueva contraseña para que no intenten guardarse en la BD.
        unset($data['new_password'], $data['new_password_confirmation']);

        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterFill(): void
    {
        if ($this->record->rol) {
            $rol = $this->record->rol->nombre;
            $grupo = match ($rol) {
                'Recepcionista', 'Gestor de Compras', 'Compras' => 'Compras',
                default => $rol,
            };

            // Obtenemos los datos actuales del formulario, añadimos el nuestro y volvemos a llenar.
            // Esto evita que se borren los datos que Filament ya cargó.
            $data = $this->form->getState();
            $data['grupo_rol'] = $grupo;
            $this->form->fill($data);
        }
    }
}
