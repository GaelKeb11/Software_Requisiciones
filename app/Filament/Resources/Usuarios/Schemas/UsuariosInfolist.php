<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Schemas\Schema;

class UsuariosInfolist
{
    public static function configure($infolist): Schema
    {
        return $infolist
            ->schema([
                Section::make('Datos Personales')
                    ->icon('heroicon-o-user-circle')
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('profile_photo_path')
                            ->label('Foto de Perfil')
                            ->circular() // Corrección: El método correcto es circular()
                            ->columnSpanFull(),
                        TextEntry::make('name')->label('Nombre(s)'),
                        TextEntry::make('apellido_paterno')->label('Apellido Paterno'),
                        TextEntry::make('apellido_materno')->label('Apellido Materno'),
                    ]),
                
                Section::make('Contacto y Acceso')
                    ->icon('heroicon-o-key')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('numero_telefonico')->label('Número de Teléfono'),
                        TextEntry::make('email')->label('Correo Electrónico'),
                    ]),

                Section::make('Asignación Organizacional')
                    ->icon('heroicon-o-building-office-2')
                    ->columns(2)
                    ->schema([
                        // Mostramos el nombre del rol a través de la relación
                        TextEntry::make('rol.nombre')
                            ->label('Rol Específico')
                            ->default('No asignado'),
                        // Mostramos el nombre del departamento a través de la relación
                        TextEntry::make('departamento.nombre')
                            ->label('Departamento')
                            ->default('No asignado'),
                    ]),
            ]);
    }
}
