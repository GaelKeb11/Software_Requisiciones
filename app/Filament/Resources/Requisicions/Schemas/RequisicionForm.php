<?php

namespace App\Filament\Resources\Requisicions\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Field;
use Filament\Schemas\Schema;
use Filament\Support\Features;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Actions\Action;

class RequisicionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('folio')->required(),

                DatePicker::make('fecha_creacion')->required(),

                DatePicker::make('fecha_recepcion')
                    ->default(now()->toDateString())
                    ->readOnly()
                    ->hidden(),

                TimePicker::make('hora_recepcion')
                    ->default(now()->format('H:i'))
                    ->readOnly()
                    ->hidden(),

                Textarea::make('concepto')->required(),

                Select::make('id_departamento')
                    ->label('Dependencia')
                    ->relationship('departamento', 'nombre')
                    ->searchable()         // activa la búsqueda AJAX
                    ->preload()            // muestra un listado inicial de opciones
                    ->searchDebounce(300)      // espera 300ms tras teclear para filtrar
                    ->required(),

                Select::make('id_clasificacion')
                    ->relationship('clasificacion', 'nombre')
                    ->searchable()         // activa la búsqueda AJAX
                    ->preload()            // muestra un listado inicial de opciones
                    ->searchDebounce(300)
                    ->required(),

                Select::make('id_usuario')
                    ->relationship('usuario', 'name'),

                // 📁 Documentos - Solo en creación
                Repeater::make('documentos')
                    ->label('Cargar Documentos')
                    ->relationship('documentos')
                    ->schema([
                        FileUpload::make('ruta_archivo')
                            ->label('Archivo')
                            ->disk('public')
                            ->directory('documentos')
                            ->storeFileNamesIn('nombre_archivo')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel.sheet.macroEnabled.12',
                                'application/pdf',
                                'image/jpeg',
                            ])
                            ->getUploadedFileNameForStorageUsing(
                                fn($file) => md5($file->getClientOriginalName() . time()) . '.' . $file->getClientOriginalExtension()
                            ),

                        Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->options([
                                'oficio' => 'Oficio',
                                'factura' => 'Factura',
                                'cotizacion' => 'Cotización',
                                'otro' => 'Otro',
                            ]),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ Agregar Documento')
                    ->collapsible()
                    ->visibleOn(['create']),

                // 📑 Documentos existentes - Solo en edición
                Repeater::make('documentos_relacionados')
                    ->label('Documentos Adjuntos')
                    ->relationship('documentos')
                    ->schema([
                        TextInput::make('nombre_archivo')
                            ->label('Nombre del Archivo')
                            ->disabled(),

                        Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->options([
                                'oficio' => 'Oficio',
                                'factura' => 'Factura',
                                'cotizacion' => 'Cotización',
                                'otro' => 'Otro',
                            ])
                            ->required(),

                        ViewField::make('descargar')
                            ->label('')
                            ->view('components.document-download')
                            ->viewData(fn ($record) => [
                                'url' => asset('storage/' . $record->ruta_archivo)
                            ])
                    ])
                    ->columns(3)
                    ->deletable()
                    ->addable(false)
                    ->reorderable(false)
                    ->visibleOn(['edit'])
            ]);
    }
}