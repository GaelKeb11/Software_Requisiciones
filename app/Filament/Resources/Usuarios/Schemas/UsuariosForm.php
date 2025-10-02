<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class UsuariosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Usuario')
                    ->schema([
                        TextInput::make('name', 'Nombre')
                            ->required(),
                        TextInput::make('apellido_paterno'),
                        TextInput::make('apellido_materno'),
                    ]),
                Section::make('Contacto')
                    ->schema([
                        TextInput::make('numero_telefonico'),
                        TextInput::make('email', 'Correo Electrónico')
                            ->email()
                            ->required(),
                        TextInput::make('password', 'Contraseña')
                            ->password()
                            ->required(),
                    ]),
                Section::make('Roles y Departamentos')
                    ->schema([
                        Select::make('id_rol')
                            ->relationship('roles', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('id_departamento')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),
            ]);
    }
}
