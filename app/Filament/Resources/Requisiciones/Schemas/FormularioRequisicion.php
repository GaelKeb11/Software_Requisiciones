<?php

namespace App\Filament\Resources\Requisiciones\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Set;
use App\Models\Recepcion\Departamento;
use App\Models\Recepcion\Estatus;
use App\Models\Compras\Cotizacion;


use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios\Usuario;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Recepcion\Documento;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Group;
use Illuminate\Http\UploadedFile as TemporaryUploadedFile;

// La importación de Heroicon se elimina, ya que no es necesaria con la sintaxis corregida.

use Filament\Schemas\Components\Section;

class FormularioRequisicion
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 3. Los campos ocultos se mantienen fuera de las pestañas para que siempre se procesen.
                DatePicker::make('fecha_recepcion')
                    ->default(now()->toDateString())
                    ->readOnly()
                    ->hidden(),

                TimePicker::make('hora_recepcion')
                    ->default(now()->format('H:i'))
                    ->readOnly()
                    ->hidden(),

                // 4. Se crea el componente principal de Pestañas (Tabs)
                Tabs::make('Crear Requisición')
                    ->tabs([
                        // 5. PESTAÑA 1: DETALLES DE LA REQUISICIÓN
                        Tab::make('Detalles de la Requisición')

                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('folio')
                                            ->label('Folio')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1)
                                            ->readOnly(function () {
                                                /** @var \App\Models\Usuarios\Usuario $user */
                                                $user = Auth::user();
                                                return !$user->esRecepcionista();
                                            })
                                            ->default(function () {
                                                /** @var \App\Models\Usuarios\Usuario $user */
                                                $user = Auth::user();
                                                if ($user->esRecepcionista()) {
                                                    return null;
                                                }

                                                $departamento = $user->departamento;
                                                if ($departamento && $departamento->prefijo) {
                                                    $prefix = $departamento->prefijo . '-' . now()->year;
                                                    $lastRequisicion = \App\Models\Recepcion\Requisicion::where('folio', 'like', $prefix . '-%')
                                                        ->latest('id_requisicion')
                                                        ->first();

                                                    $nextNumber = 1;
                                                    if ($lastRequisicion) {
                                                        $lastNumber = (int) last(explode('-', $lastRequisicion->folio));
                                                        $nextNumber = $lastNumber + 1;
                                                    }

                                                    $folioNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                                                    return $prefix . '-' . $folioNumber;
                                                }
                                                return null;
                                            }),
                                        DatePicker::make('fecha_creacion')
                                            ->label('Fecha de Creación')
                                            ->required()->columnSpan(1)
                                            ->disabled(fn ($record) => $record && $record->id_estatus >= 2),
                                        Select::make('id_departamento')
                                            ->label('Dependencia')
                                            ->relationship('departamento', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->disabled(function () {
                                                /** @var \App\Models\Usuarios\Usuario $user */
                                                $user = Auth::user();
                                                return !$user->esRecepcionista();
                                            })
                                            ->default(function () {
                                                /** @var \App\Models\Usuarios\Usuario $user */
                                                $user = Auth::user();
                                                return !$user->esRecepcionista() ? $user->id_departamento : null;
                                            })
                                            ->live()
                                            ->afterStateUpdated(function ($set, $state) {
                                                /** @var \App\Models\Usuarios\Usuario $user */
                                                $user = Auth::user();
                                                if ($user->esRecepcionista()) {
                                                    if ($state) {
                                                        $departamento = \App\Models\Recepcion\Departamento::find($state);
                                                        if ($departamento && $departamento->prefijo) {
                                                            $set('folio', $departamento->prefijo . '-' . now()->year . '-');
                                                        } else {
                                                            $set('folio', '');
                                                        }
                                                    } else {
                                                        $set('folio', '');
                                                    }
                                                }
                                            })
                                            ->columnSpan(1),
                                        Select::make('id_clasificacion')
                                            ->label('Clasificación')
                                            ->relationship('clasificacion', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->columnSpan(1)
                                            ->visible(fn ($record) => !$record || $record->id_estatus < 2),
                                        TextInput::make('clasificacion_nombre')
                                            ->label('Clasificación')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(1)
                                            ->visible(fn ($record) => $record && $record->id_estatus >= 2)
                                            ->afterStateHydrated(function (TextInput $component, $record) {
                                                $component->state($record?->clasificacion?->nombre);
                                            }),
                                        Textarea::make('concepto')
                                            ->label('Concepto')
                                            ->required()
                                            ->minLength(10)
                                            ->maxLength(500)
                                            ->columnSpanFull()
                                            ->disabled(fn ($record) => $record && $record->id_estatus >= 2),
                                    ])->columns(2),
                                Select::make('id_usuario')
                                    ->relationship('usuario', 'name', function ($query) {
                                        return $query->whereHas('rol', function ($query) {
                                            $query->where('nombre', 'Gestor de Compras');
                                        });
                                    })
                                    ->label('Asignado a')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull()
                                    ->disabled(fn ($record) => $record && $record->id_estatus >= 2 && !Auth::user()->rol->nombre == 'Gestor de Compras'),
                            ]),

                        // 6. PESTAÑA 2: DOCUMENTOS
                        Tab::make('Documentos')
                            ->icon('heroicon-o-document-plus')
                            ->schema([
                                Repeater::make('documentos')
                                    ->relationship()
                                    ->schema([
                                        Select::make('tipo_documento')
                                            ->label('Tipo de Documento')
                                            ->options([
                                                'Requisición' => 'Requisición',
                                            ])
                                            ->required(),
                                        FileUpload::make('ruta_archivo')
                                            ->label('Documento')
                                            ->required()
                                            ->storeFileNamesIn('nombre_archivo')
                                            ->disk('public')
                                            ->directory('requisiciones-documentos')
                                            ->visibility('public')
                                            ->downloadable()
                                            ->openable(),
                                        Textarea::make('comentarios')
                                            ->label('Comentarios')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('Agregar Documento')
                                    ->itemLabel(fn (array $state): ?string => $state['nombre_archivo'] ?? null)
                                    ->disabled(function ($record) {
                                        if (!$record || $record->id_estatus < 2) return false;
                                        /** @var \App\Models\Usuarios\Usuario $user */
                                        $user = Auth::user();
                                        if ($user->rol->nombre === 'Gestor de Compras' && in_array($record->id_estatus, [3, 5])) {
                                            return false;
                                        }
                                        if ($user->esRecepcionista()) return false; // Optional assumption
                                        return true; // Default disabled for others (Solicitante) if status >= 2
                                    })
                                    ->deletable(fn ($record) => !($record && $record->id_estatus >= 2)),
                            ]),
                    ])->columnSpanFull(), // Asegura que las pestañas ocupen todo el ancho
            ]);
    }
}
