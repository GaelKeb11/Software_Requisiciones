<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Mask;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\FileUpload;


class UsuariosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del Usuario')
                ->schema([
                    TextInput::make('name')
                        ->label('Nombre(s)')
                        ->required()
                        ->maxLength(255)
                        // Validación Regex para permitir solo letras (incluyendo acentos) y espacios.
                        ->regex('/^[\pL\s\-]+$/u'), 
                    
                    TextInput::make('apellido_paterno')
                        ->label('Apellido Paterno')
                        ->maxLength(255)
                        ->regex('/^[\pL\s\-]+$/u'),

                    TextInput::make('apellido_materno')
                        ->label('Apellido Materno')
                        ->maxLength(255)
                        ->regex('/^[\pL\s\-]+$/u'),

                        FileUpload::make('profile_photo_path')
                        ->label('Foto de Perfil')
                        ->image()
                        ->avatar() // Estilo circular
                        ->directory('profile-photos') // Carpeta donde se guardará
                        ->disk('public'),
                ])->columns(3), // Organiza los campos de nombre en 3 columnas
 
                
                Section::make('Información de Contacto y Acceso')
                ->schema([
                    TextInput::make('numero_telefonico')
                        ->label('Número de Teléfono')
                        ->required()
                        ->tel() // Mejora la experiencia en móviles
                        ->numeric()
                        ->rule('digits:10') // Valida que sean exactamente 10 dígitos
                        ->unique(ignoreRecord: true)
                        // Se usa un valor no estándar para forzar la desactivación del autocompletado.
                        ->extraAttributes(['autocomplete' => 'nope']),
            
                    TextInput::make('email')
                        ->label('Correo Electrónico')
                        ->required()
                        ->email() // Valida el formato de email
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        // Se usa un valor no estándar para forzar la desactivación del autocompletado.
                        ->extraAttributes(['autocomplete' => 'nope']),
            
                    TextInput::make('password')
                        ->label('Contraseña')
                        ->password()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->rule(Password::min(8)->mixedCase()->numbers())
                        ->confirmed()
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        // Se usa un valor no estándar para evitar que los gestores de contraseñas autocompleten.
                        ->extraAttributes(['autocomplete' => 'new-password-field']),
                    
                    TextInput::make('password_confirmation')
                        ->label('Confirmar Contraseña')
                        ->password()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(false)
                        // Se usa un valor no estándar para evitar que los gestores de contraseñas autocompleten.
                        ->extraAttributes(['autocomplete' => 'new-password-field']),
                ])->columns(2),

                    
                    Section::make('Asignación Organizacional')
                    ->schema([
                        Select::make('id_rol')
                            ->label('Rol')
                            ->relationship('rol', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('id_departamento')
                            ->label('Departamento')
                            ->relationship('departamento', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
