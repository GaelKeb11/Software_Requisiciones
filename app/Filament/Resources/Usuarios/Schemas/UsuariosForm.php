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
use Filament\Forms\Components\Textarea;
 

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
                            ])->columns([
                                'default' => 1,
                                'md' => 3,
                            ]),

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
                            ])->columns([
                                'default' => 1,
                                'md' => 2,
                            ]),

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
                                    'DireccionAdministracion' => 'Personal de Dirección de Administración',
                                    'Solicitante' => 'Solicitantes',
                                    'Director' => 'Directores',
                                    'Tesoreria' => 'Tesorería',
                                ])
                                ->live()
                                ->required() // Este campo ahora es obligatorio
                                ->dehydrated(false)
                                ->afterStateUpdated(function ($set, $state) {
                                    // Resetear campos dependientes
                                    $set('id_rol', null);
                                    $set('id_departamento', null);

                                    // Pre-seleccionar el rol si es único para el grupo y establecerlo reactivamente
                                    if (in_array($state, ['Solicitante', 'Director', 'Tesoreria'])) {
                                        $roleName = $state === 'Tesoreria' ? 'Tesorería' : $state;
                                        $rol = Rol::where('nombre', $roleName)->first();
                                        if ($rol) {
                                            $set('id_rol', $rol->id_rol);
                                        }

                                        if ($state === 'Tesoreria') {
                                            $dept = Departamento::where('nombre', 'LIKE', '%Tesorería%')
                                                ->orWhere('nombre', 'LIKE', '%Tesoreria%')
                                                ->first();
                                            if ($dept) {
                                                $set('id_departamento', $dept->id_departamento);
                                            }
                                        }
                                    }
                                }),
                    
                            Select::make('id_rol')
                                ->label('Rol Específico')
                                ->options(function ($get) {
                                    $grupo = $get('grupo_rol');
                                    if ($grupo === 'DireccionAdministracion') {
                                        return Rol::whereIn('nombre', ['Recepcionista', 'Gestor de Administración', 'Gestor de Compras', 'Director'])->pluck('nombre', 'id_rol');
                                    }
                                    if (in_array($grupo, ['Solicitante', 'Director', 'Tesoreria'])) {
                                        $roleName = $grupo === 'Tesoreria' ? 'Tesorería' : $grupo;
                                        return Rol::where('nombre', $roleName)->pluck('nombre', 'id_rol');
                                    }
                                    return [];
                                })
                                ->disabled(fn ($get) => in_array($get('grupo_rol'), ['Solicitante', 'Director', 'Tesoreria']))
                                ->dehydrated()
                                ->live()
                                ->afterStateUpdated(function ($get, $set, $state) {
                                    // Si se ha seleccionado un rol dentro del grupo de Dirección de Administración,
                                    // se asigna automáticamente ese departamento.
                                    if ($get('grupo_rol') === 'DireccionAdministracion' && filled($state)) {
                                        $dirAdmDept = Departamento::where('nombre', 'Dirección de Administración')->first();
                                        $set('id_departamento', $dirAdmDept?->id_departamento);
                                    }
                                })
                                ->required()
                                ->visible(fn ($get) => filled($get('grupo_rol'))),
                    
                            Select::make('id_departamento')
                                ->label('Departamento')
                                ->options(function ($get) {
                                    $grupo = $get('grupo_rol');
                                    $query = Departamento::query();

                                    switch ($grupo) {
                                        case 'DireccionAdministracion':
                                            return $query->where('nombre', 'Dirección de Administración')->pluck('nombre', 'id_departamento');

                                        case 'Tesoreria':
                                            return $query->where('nombre', 'LIKE', '%Tesorería%')
                                                ->orWhere('nombre', 'LIKE', '%Tesoreria%')
                                                ->pluck('nombre', 'id_departamento');

                                        case 'Solicitante':
                                            return $query->pluck('nombre', 'id_departamento');

                                        case 'Director':
                                            $directorRolId = Rol::where('nombre', 'Director')->value('id_rol');
                                            $userId = $get('id_usuario');

                                            $subQuery = Usuario::where('id_rol', $directorRolId)->whereNotNull('id_departamento');
                                            if ($userId) {
                                                $subQuery->where('id_usuario', '!=', $userId);
                                            }
                                            $occupiedDeptoIds = $subQuery->pluck('id_departamento');

                                            return $query->whereNotIn('id_departamento', $occupiedDeptoIds)->pluck('nombre', 'id_departamento');
                                        
                                        default:
                                            return [];
                                    }
                                })
                                //->disabled(fn ($get) => in_array($get('grupo_rol'), ['Solicitante', 'Director']))
                                ->dehydrated() // Se asegura que se guarde el valor aunque esté deshabilitado
                                ->searchable(fn ($get) => in_array($get('grupo_rol'), ['Solicitante', 'Director']))
                                ->required()
                                ->visible(fn ($get) => filled($get('grupo_rol'))),
                    
                        ]),
                    ])->columnSpanFull(),
            ]);
    }
}
