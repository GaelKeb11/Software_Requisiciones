<?php

namespace App\Filament\Resources\Usuarios\Schemas;

use App\Models\Recepcion\Departamento;
use App\Models\Usuarios\Rol;
use App\Models\Usuarios\Usuario;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Password;
 

class UsuariosForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Usuario')
                    ->tabs([
                        // PESTAÑA 1: DATOS PERSONALES
                        Tab::make('Datos Personales')
                            ->icon('heroicon-o-user-circle')
                            ->schema([
                                FileUpload::make('profile_photo_path')
                                    ->label('Foto de Perfil')
                                    ->image()
                                    ->avatar()
                                    ->directory('profile-photos')
                                    ->disk('public')
                                    ->maxSize(1024) // Limita el tamaño a 1MB
                                    ->columnSpanFull(),
                                
                                TextInput::make('name')
                                    ->label('Nombre(s)')
                                    ->required()
                                    ->maxLength(255)
                                    ->regex('/^[\pL\s\-]+$/u'),
                                
                                TextInput::make('apellido_paterno')
                                    ->label('Apellido Paterno')
                                    ->required()
                                    ->maxLength(255)
                                    ->regex('/^[\pL\s\-]+$/u'),

                                TextInput::make('apellido_materno')
                                    ->label('Apellido Materno')
                                    ->required()
                                    ->maxLength(255)
                                    ->regex('/^[\pL\s\-]+$/u'),
                            ])->columns(3),

                        // PESTAÑA 2: CONTACTO Y ACCESO
                        Tab::make('Contacto y Acceso')
                            ->icon('heroicon-o-key')
                            ->schema([
                                TextInput::make('numero_telefonico')
                                    ->label('Número de Teléfono')
                                    ->required()
                                    ->tel()
                                    ->numeric()
                                    ->rule('digits:10')
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->extraAttributes(['autocomplete' => 'nope']),
                                
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->required()
                                    ->email()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->extraAttributes(['autocomplete' => 'nope']),
                                
                                    TextInput::make('password')
                                    ->label('Contraseña')
                                    ->password()
                                    ->revealable() // <-- AÑADE ESTA LÍNEA
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->rule(Password::min(8)->mixedCase()->numbers())
                                    ->confirmed()
                                    ->dehydrated(fn ($state, string $operation): bool => $operation === 'create' || filled($state))
                                    ->visible(fn (string $operation): bool => $operation === 'create')
                                    ->extraAttributes(['autocomplete' => 'new-password-field']),
                                
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Contraseña')
                                    ->password()
                                    ->revealable() // <-- AÑADE ESTA LÍNEA
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                                    ->visible(fn (string $operation): bool => $operation === 'create')
                                    ->extraAttributes(['autocomplete' => 'new-password-field']),
                            ])->columns(2),

                        // PESTAÑA 4: SEGURIDAD (SOLO EN EDICIÓN)
                        Tab::make('Seguridad')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                TextInput::make('new_password')
                                    ->label('Nueva Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->rule(Password::min(8)->mixedCase()->numbers())
                                    ->confirmed()
                                    ->dehydrated(false),

                                TextInput::make('new_password_confirmation')
                                    ->label('Confirmar Nueva Contraseña')
                                    ->password()
                                    ->revealable()
                                    ->dehydrated(false),
                            ])
                            ->visible(fn (string $operation): bool => $operation === 'edit'),

                        // PESTAÑA 3: ASIGNACIÓN ORGANIZACIONAL
                        Tab::make('Asignación Organizacional')
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Select::make('grupo_rol')
                                ->label('Grupo de Rol')
                                ->options([
                                    'Compras' => 'Personal de Compras',
                                    'Solicitante' => 'Solicitantes',
                                    'Director' => 'Directores',
                                ])
                                ->live()
                                ->required() // Este campo ahora es obligatorio
                                ->dehydrated(false)
                                ->afterStateUpdated(function ($set, $state) {
                                    // Resetear campos dependientes
                                    $set('id_rol', null);
                                    $set('id_departamento', null);

                                    // Pre-seleccionar el rol si es único para el grupo y establecerlo reactivamente
                                    if (in_array($state, ['Solicitante', 'Director'])) {
                                        $rol = Rol::where('nombre', $state)->first();
                                        if ($rol) {
                                            $set('id_rol', $rol->id_rol);
                                        }
                                    }
                                }),
                    
                            Select::make('id_rol')
                                ->label('Rol Específico')
                                ->options(function ($get) {
                                    $grupo = $get('grupo_rol');
                                    if ($grupo === 'Compras') {
                                        return Rol::whereIn('nombre', ['Recepcionista', 'Gestor de Compras', 'Director'])->pluck('nombre', 'id_rol');
                                    }
                                    if (in_array($grupo, ['Solicitante', 'Director'])) {
                                        return Rol::where('nombre', $grupo)->pluck('nombre', 'id_rol');
                                    }
                                    return [];
                                })
                                ->disabled(fn ($get) => in_array($get('grupo_rol'), ['Solicitante', 'Director']))
                                ->dehydrated()
                                ->live()
                                ->afterStateUpdated(function ($get, $set, $state) {
                                    // Si el rol es de Compras, pre-llenar y bloquear el departamento
                                    if ($get('grupo_rol') === 'Compras') {
                                        $rol = $state ? Rol::find($state) : null;
                                        if ($rol && in_array($rol->nombre, ['Recepcionista', 'Gestor de Compras', 'Director'])) {
                                            $comprasDept = Departamento::where('nombre', 'Compras')->first();
                                            $set('id_departamento', $comprasDept?->id_departamento);
                                        }
                                    }
                                })
                                ->required()
                                ->visible(fn ($get) => filled($get('grupo_rol'))),
                    
                            Select::make('id_departamento')
                                ->label('Departamento')
                                ->options(function ($get) {
                                    $grupo = $get('grupo_rol');
                                    $query = Departamento::query();

                                    if ($grupo === 'Compras') {
                                        return $query->where('nombre', 'Compras')->pluck('nombre', 'id_departamento');
                                    }

                                    if ($grupo === 'Solicitante') {
                                        return $query->pluck('nombre', 'id_departamento');
                                    }

                                    if ($grupo === 'Director') {
                                        $directorRolId = Rol::where('nombre', 'Director')->value('id_rol');
                                        $userId = $get('id_usuario');

                                        $subQuery = Usuario::where('id_rol', $directorRolId)->whereNotNull('id_departamento');
                                        if ($userId) {
                                            $subQuery->where('id_usuario', '!=', $userId);
                                        }
                                        $occupiedDeptoIds = $subQuery->pluck('id_departamento');

                                        return $query->whereNotIn('id_departamento', $occupiedDeptoIds)->pluck('nombre', 'id_departamento');
                                    }
                                    
                                    return [];
                                })
                                ->disabled(fn ($get) => $get('grupo_rol') === 'Compras')
                                ->searchable(fn ($get) => in_array($get('grupo_rol'), ['Solicitante', 'Director']))
                                ->required()
                                ->visible(fn ($get) => filled($get('grupo_rol'))),
                        ]),
                    ])->columnSpanFull(),
            ]);
    }
}
