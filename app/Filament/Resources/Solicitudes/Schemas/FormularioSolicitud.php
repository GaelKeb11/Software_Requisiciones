<?php

namespace App\Filament\Resources\Solicitudes\Schemas;

use App\Models\Recepcion\Clasificacion;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class FormularioSolicitud
{
    public static function configure(Schema $schema): Schema
    {
        $user = Auth::user();

        return $schema->schema([
            Tabs::make('Crear Solicitud')
                ->disabled(function ($record) {
                    if (!$record) return false;
                    // Bloquear si el estatus no es Borrador (1), para todos los usuarios (incluido Admin)
                    return $record->id_estatus != 1;
                })
                ->tabs([
                    Tab::make('Información General')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            TextInput::make('solicitante_display')
                                ->label('Solicitante')
                                ->disabled()
                                ->dehydrated(false)
                                ->formatStateUsing(function ($state, $record) use ($user) {
                                    if ($record && $record->solicitante) {
                                        $solicitante = $record->solicitante;
                                        return trim("{$solicitante->name} {$solicitante->apellido_paterno} {$solicitante->apellido_materno}");
                                    }

                                    return $user
                                        ? trim("{$user->name} {$user->apellido_paterno} {$user->apellido_materno}")
                                        : 'Sin asignar';
                                }),
                            DatePicker::make('fecha_creacion')
                                ->label('Fecha de Solicitud')
                                ->default(now())
                                ->disabled()
                                ->dehydrated(true),
                            Textarea::make('concepto')
                                ->label('Concepto General')
                                ->required()
                                ->minLength(10) // Validación de longitud mínima
                                ->maxLength(500) // Validación de longitud máxima
                                ->columnSpanFull(),
                            Select::make('id_clasificacion')
                                ->label('Clasificación')
                                ->options(fn ($get) => self::getGeneralClasificacionesOptions((int) $get('id_clasificacion')))
                                ->disableOptionWhen(fn ($value) => now()->day > 8 && (int) $value === 2161)
                                ->searchable()
                                ->preload()
                                ->required()
                                ->helperText('La clasificación 2161 (Material de limpieza) solo está disponible los primeros 8 días de cada mes.')
                                ->columnSpanFull(),
                            Hidden::make('id_estatus')
                                ->default(1),
                        ])
                        ->columns([
                            'default' => 1,
                            'md' => 3,
                        ]),

                    Tab::make('Artículos Solicitados')
                        ->icon('heroicon-o-shopping-cart')
                        ->schema([
                            Repeater::make('detalles')
                                ->relationship()
                                ->label('')
                                ->schema([
                                    TextInput::make('cantidad')
                                        ->label('Cantidad')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1) // Validación de valor mínimo
                                        ->default(1),
                                    Select::make('Tipo de Unidad')
                                        ->label('Tipo de Unidad')
                                        ->options([
                                            'Materiales	' => 'Materiales',
                                            'Servicios' => 'Servicios',
                                            'Equipos' => 'Equipos',
                                            'Otro' => 'Otros',
                                        ])
                                        ->required(),
                                    Select::make('id_clasificacion_detalle')
                                        ->label('Clasificación específica')
                                        ->required()
                                        ->options(function (Get $get) {
                                            $generalId = (int) $get('../../id_clasificacion');
                                            $current = (int) $get('id_clasificacion_detalle');

                                            return self::getClasificacionesEspecificasPorGeneral($generalId, $current);
                                        })
                                        ->disableOptionWhen(fn ($value) => now()->day > 8 && (int) $value === 2161)
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->columnSpan(2)
                                        ->helperText('Selecciona la clasificación correspondiente a la general elegida.'),
                                    TextInput::make('descripcion')
                                        ->label('Descripción del Artículo')
                                        ->required()
                                        ->minLength(5) // Validación de longitud mínima
                                        ->maxLength(255) // Validación de longitud máxima
                                        ->columnSpan(2),

                                ])
                                ->addActionLabel('+ Añadir Artículo')
                                ->columns([
                                    'default' => 1,
                                    'sm' => 2,
                                    'lg' => 4,
                                ])
                                ->defaultItems(1)
                                ->required()
                                ->collapsible(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function getGeneralClasificacionesOptions(?int $currentId = null): array
    {
        $generalesPermitidas = [2000, 3000, 5000];

        $clasificaciones = Clasificacion::all()
            ->filter(function (Clasificacion $clasificacion) use ($generalesPermitidas) {
                $numero = self::extraerNumeroDesdeNombre($clasificacion->nombre);
                return $numero !== null && in_array($numero, $generalesPermitidas, true);
            });

        if (now()->day > 8) {
            $clasificaciones = $clasificaciones->reject(function (Clasificacion $clasificacion) {
                return (int) $clasificacion->id_clasificacion === 2161;
            });

            if ($currentId === 2161) {
                $actual = Clasificacion::find($currentId);
                if ($actual) {
                    $clasificaciones->push($actual);
                }
            }
        }

        return $clasificaciones
            ->sortBy('nombre')
            ->pluck('nombre', 'id_clasificacion')
            ->all();
    }

    public static function getClasificacionesEspecificasPorGeneral(?int $generalId, ?int $currentId = null): array
    {
        if (!$generalId) {
            return [];
        }

        $general = Clasificacion::find($generalId);
        if (!$general) {
            return [];
        }

        $numeroGeneral = self::extraerNumeroDesdeNombre($general->nombre);
        if ($numeroGeneral === null) {
            return [];
        }

        $rangeStart = (int) (floor($numeroGeneral / 1000) * 1000);
        $rangeEnd = $rangeStart + 999;

        $clasificaciones = Clasificacion::all()
            ->filter(function (Clasificacion $clasificacion) use ($rangeStart, $rangeEnd) {
                $numero = self::extraerNumeroDesdeNombre($clasificacion->nombre);
                return $numero !== null && $numero >= $rangeStart && $numero <= $rangeEnd;
            });

        if (now()->day > 8) {
            $clasificaciones = $clasificaciones->reject(function (Clasificacion $clasificacion) {
                return (int) $clasificacion->id_clasificacion === 2161;
            });

            if ($currentId === 2161) {
                $actual = Clasificacion::find($currentId);
                if ($actual) {
                    $clasificaciones->push($actual);
                }
            }
        }

        return $clasificaciones
            ->sortBy('nombre')
            ->pluck('nombre', 'id_clasificacion')
            ->all();
    }

    private static function extraerNumeroDesdeNombre(?string $nombre): ?int
    {
        if (!$nombre) {
            return null;
        }

        if (preg_match('/^\s*(\d{3,4})/', $nombre, $coincidencias)) {
            return (int) $coincidencias[1];
        }

        return null;
    }
}
