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
                            // CORRECCIÓN: Se usa el nombre completo del ícono como una cadena de texto.
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                // Todos los campos principales van aquí dentro
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
                                    ->required()->columnSpan(1),
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
                                    ->columnSpan(1),
                                Textarea::make('concepto')
                                    ->label('Concepto')
                                    ->required()
                                    ->minLength(10) // Validación de longitud mínima
                                    ->maxLength(500) // Validación de longitud máxima
                                    ->columnSpanFull(),
                                Select::make('id_usuario')
                                    ->relationship('usuario', 'name', function ($query) {
                                        return $query->whereHas('rol', function ($query) {
                                            $query->where('nombre', 'Gestor de Compras');
                                        });
                                    })
                                    ->label('Asignado a')
                                    ->searchable()
                                    ->preload()
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpanFull(), // Asegura que las pestañas ocupen todo el ancho
            ]);
    }
}
