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
                                    ->maxLength(255) // Validación de longitud máxima
                                    ->columnSpan(1),
                                DatePicker::make('fecha_creacion')
                                    ->label('Fecha de Creación')
                                    ->required()->columnSpan(1),
                                Select::make('id_departamento')
                                    ->label('Dependencia')
                                    ->relationship('departamento', 'nombre')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state) {
                                        if ($state) {
                                            $departamento = Departamento::find($state);
                                            if ($departamento && $departamento->prefijo) {
                                                $set('folio', $departamento->prefijo . '-' . now()->year . '-');
                                            } else {
                                                $set('folio', null);
                                            }
                                        } else {
                                            $set('folio', null);
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

                        // 6. PESTAÑA 2: DOCUMENTOS ADJUNTOS
                        Tab::make('Documentos Adjuntos')
                            // CORRECCIÓN: Se usa el nombre completo del ícono como una cadena de texto.
                            ->icon('heroicon-o-paper-clip')
                            ->schema([
                                // Ambos Repeaters (para crear y editar) van en esta pestaña.
                                // Filament mostrará solo el que corresponda gracias a la lógica 'visibleOn'.
                                Repeater::make('documentos')
                                    ->label('Cargar Nuevos Documentos')
                                    ->relationship('documentos')
                                    ->schema([
                                        FileUpload::make('ruta_archivo')
                                            ->label('Archivo')
                                            ->disk('public')
                                            ->directory('documentos')
                                            ->storeFileNamesIn('nombre_archivo')
                                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                                            ->getUploadedFileNameForStorageUsing(
                                                fn($file) => md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension()
                                            ),
                                        Select::make('tipo_documento')
                                            ->label('Tipo de Documento')
                                            ->options(['oficio' => 'Oficio', 'factura' => 'Factura', 'cotizacion' => 'Cotización', 'otro' => 'Otro'])
                                    ])
                                    ->columns(2)
                                    ->addActionLabel('+ Agregar Documento')
                                    ->collapsible()
                                    ->visibleOn('create'),

                                Repeater::make('documentos_relacionados')
                                    ->label('Documentos Existentes')
                                    ->relationship('documentos')
                                    ->schema([
                                        TextInput::make('nombre_archivo')->label('Nombre del Archivo')->disabled(),
                                        Select::make('tipo_documento')->label('Tipo de Documento')->options(['oficio' => 'Oficio', 'factura' => 'Factura', 'cotizacion' => 'Cotización', 'otro' => 'Otro'])->required(),
                                        ViewField::make('descargar')->label('')->view('components.document-download')->viewData(fn ($record) => ['url' => asset('storage/' . $record->ruta_archivo)])
                                    ])
                                    ->columns(3)
                                    ->deletable()
                                    ->addable(false)
                                    ->reorderable(false)
                                    ->visibleOn('edit'),
                            ]),
                    ])->columnSpanFull(), // Asegura que las pestañas ocupen todo el ancho
            ]);
    }
}
