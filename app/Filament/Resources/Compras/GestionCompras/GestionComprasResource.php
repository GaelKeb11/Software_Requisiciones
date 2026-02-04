<?php

namespace App\Filament\Resources\Compras\GestionCompras;

use App\Filament\Resources\Compras\GestionCompras\Pages;
use App\Models\Recepcion\Requisicion;

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;


use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\Compras\GestionCompras\Pages\ViewGestionCompras;

use BackedEnum;
use UnitEnum;

class GestionComprasResource extends Resource
{
    protected static ?string $model = Requisicion::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Gestión Dirección de Administración';
    protected static ?string $slug = 'gestion-direccion-administracion';
    protected static ?string $modelLabel = 'Requisición para Dirección de Administración';
    protected static ?string $pluralModelLabel = 'Requisiciones para Dirección de Administración';
    protected static string|UnitEnum|null $navigationGroup = 'Dirección de Administración';


    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return in_array($user->rol->nombre, ['Gestor de Administración', 'Gestor de Compras', 'Administrador']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Requisición y Cotización')
                    ->tabs([
                        // TAB 1: INFORMACIÓN GENERAL
                        Tab::make('Información General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('folio')->label('Folio')->disabled(),
                                        TextInput::make('fecha_creacion')->label('Fecha Creación')->disabled(),
                                        TextInput::make('concepto')->label('Concepto')->columnSpan(3)->disabled(),
                                        TextInput::make('id_solicitante')
                                            ->label('Solicitante')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->formatStateUsing(fn (?Requisicion $record) => $record?->solicitante?->name . ' ' . $record?->solicitante?->apellido_paterno . ' ' . $record?->solicitante?->apellido_materno ?? 'Sin asignar'),
                                        TextInput::make('id_departamento')
                                            ->label('Departamento')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->formatStateUsing(fn (?Requisicion $record) => $record?->departamento?->nombre ?? 'Sin asignar'),
                                    ])
                            ]),

                        // TAB 2: DETALLES (ITEMS)
                        Tab::make('Detalles de Items')
                            ->icon('heroicon-o-list-bullet')
                            ->visible(fn (Requisicion $record) => $record->detalles()->exists())
                            ->schema([
                                Repeater::make('cotizaciones')
                                    ->relationship()
                                    ->label('Cotización por Items')
                                    ->schema([
                                        Section::make('Datos del Proveedor')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('nombre_proveedor')
                                                    ->label('Nombre del Proveedor')
                                                    ->required()
                                                    ->columnSpan(1),
                                                DatePicker::make('fecha_cotizacion')
                                                    ->label('Fecha de Cotización')
                                                    ->default(now())
                                                    ->required()
                                                    ->columnSpan(1),
                                            ]),

                                        Placeholder::make('items_header')
                                            ->label('')
                                            ->content(new HtmlString('
                                                <div class="grid grid-cols-4 gap-4 border-b pb-2 mb-2">
                                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-300 col-span-2">Descripción</div>
                                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-300">U.M.</div>
                                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-300">Cantidad</div>
                                                </div>
                                            ')),

                                        Repeater::make('detalles')
                                            ->relationship()
                                            ->label('Ítems a Cotizar')
                                            ->labelHidden()
                                            ->schema([
                                                TextInput::make('descripcion')->labelHidden()->disabled()->columnSpan(2),
                                                TextInput::make('unidad_medida')->labelHidden()->disabled()->columnSpan(1),
                                                TextInput::make('cantidad_cotizada')->labelHidden()->numeric()->disabled()->columnSpan(1),
                                            ])
                                            ->columns(4)
                                            
                                            ->addable(false)
                                            ->deletable(false)
                                            ->visible(fn ($record) => $record->requisicion && $record->requisicion->detalles()->exists()),

                                        Section::make('Adjuntos de Cotización')
                                            ->description('Sube el PDF o imagen que respalda esta cotización.')
                                            ->schema([
                                                Repeater::make('adjuntos')
                                                    ->relationship()
                                                    ->label('Archivos')
                                                    ->schema([
                                                        FileUpload::make('ruta_archivo')
                                                            ->label('Archivo de cotización')
                                                            ->disk('public')
                                                            ->directory('cotizaciones')
                                                            ->preserveFilenames()
                                                            ->acceptedFileTypes([
                                                                'application/pdf',
                                                                'image/jpeg',
                                                                'image/jpg',
                                                                'image/png',
                                                            ])
                                                            ->maxSize(10240)
                                                            ->storeFileNamesIn('nombre_archivo')
                                                            ->required()
                                                            ->downloadable()
                                                            ->openable()
                                                            ->columnSpanFull(),
                                                        Hidden::make('nombre_archivo'),
                                                        TextInput::make('comentarios')
                                                            ->label('Notas')
                                                            ->maxLength(255),
                                                    ])
                                                    ->minItems(1)
                                                    ->defaultItems(1)
                                                    ->addActionLabel('Agregar otro adjunto')
                                                    ->deletable(true)
                                                    ->reorderable(false)
                                                    ->collapsed(false)
                                            ]),
                                    ])
                                    ->maxItems(1)
                                    ->disableItemCreation()
                                    ->disableItemDeletion(),
                            ]),

                        // TAB 3: DOCUMENTOS (Cotización PDF y Visualización)
                        Tab::make('Documentos')
                            ->icon('heroicon-o-document-text')
                            ->columns(2)
                            ->schema([
                                // Columna Izquierda: Visualización de Documentos de Solicitud
                                Section::make('Documentos de Solicitud')
                                    ->columnSpan(1)
                                    ->description('Documentos cargados originalmente por el solicitante.')
                                    ->schema([
                                        Placeholder::make('lista_documentos_solicitud')
                                            ->label('')
                                            ->content(fn (Requisicion $record) => new HtmlString(
                                                collect($record->documentos->where('tipo_documento', 'Requisición'))->map(function($doc) {
                                                    $url = Storage::url($doc->ruta_archivo);
                                                    return "
                                                        <div class='mb-4 p-2 border rounded'>
                                                            <p class='font-bold text-sm mb-2'>{$doc->nombre_archivo}</p>
                                                            <iframe src='{$url}' width='100%' height='400px' style='border: none;'></iframe>
                                                            <div class='mt-2 text-right'>
                                                                <a href='{$url}' target='_blank' class='text-primary-600 hover:underline text-sm'>Abrir en nueva pestaña</a>
                                                            </div>
                                                        </div>
                                                    ";
                                                })->join('') ?: '<p class="text-gray-500 italic">No hay documentos de solicitud adjuntos.</p>'
                                            ))
                                    ]),

                                // Columna Derecha: Adjuntos de Cotización
                                Section::make('Adjuntos de Cotización')
                                    ->columnSpan(1)
                                    ->description('Visualiza los archivos cargados para la cotización.')
                                    ->schema([
                                        Placeholder::make('lista_adjuntos_cotizacion')
                                            ->label('')
                                            ->content(fn (Requisicion $record) => new HtmlString(
                                                (function () use ($record) {
                                                    $adjuntos = $record->cotizaciones
                                                        ->flatMap(fn ($cotizacion) => $cotizacion->adjuntos)
                                                        ->map(function ($adjunto) {
                                                            $url = Storage::url($adjunto->ruta_archivo);
                                                            $nombre = $adjunto->nombre_archivo ?: basename($adjunto->ruta_archivo);
                                                            $comentarios = $adjunto->comentarios ? "<p class='text-sm mb-2 italic'>{$adjunto->comentarios}</p>" : '';
                                                            return "
                                                                <div class='mb-4 p-2 border rounded'>
                                                                    <p class='font-bold text-sm mb-2'>Archivo: {$nombre}</p>
                                                                    {$comentarios}
                                                                    <iframe src='{$url}' width='100%' height='400px' style='border: none;'></iframe>
                                                                    <div class='mt-2 text-right'>
                                                                        <a href='{$url}' target='_blank' class='text-primary-600 hover:underline text-sm'>Abrir en nueva pestaña</a>
                                                                    </div>
                                                                </div>
                                                            ";
                                                        })
                                                        ->join('');

                                                    // Compatibilidad: mostrar documentos históricos de tipo Cotización
                                                    $documentosAntiguos = $record->documentos
                                                        ->where('tipo_documento', 'Cotización')
                                                        ->map(function ($doc) {
                                                            $url = Storage::url($doc->ruta_archivo);
                                                            return "
                                                                <div class='mb-4 p-2 border rounded'>
                                                                    <p class='font-bold text-sm mb-2'>{$doc->nombre_archivo}</p>
                                                                    <p class='text-sm mb-2 italic'>{$doc->comentarios}</p>
                                                                    <iframe src='{$url}' width='100%' height='400px' style='border: none;'></iframe>
                                                                    <div class='mt-2 text-right'>
                                                                        <a href='{$url}' target='_blank' class='text-primary-600 hover:underline text-sm'>Abrir en nueva pestaña</a>
                                                                    </div>
                                                                </div>
                                                            ";
                                                        })
                                                        ->join('');

                                                    return $adjuntos ?: ($documentosAntiguos ?: '<p class="text-gray-500 italic">No hay cotizaciones adjuntas.</p>');
                                                })()
                                            ))
                                    ]),

                                Section::make('Órdenes de Compra')
                                    ->columnSpan(1)
                                    ->visible(fn (Requisicion $record) => in_array($record->id_estatus, [5, 6, 7, 8]))
                                    ->description('Suba aquí el PDF de la orden de compra generada.')
                                    ->schema([
                                        Repeater::make('documentos_orden_compra')
                                            ->relationship('documentos', fn ($query) => $query->where('tipo_documento', 'Orden de Compra'))
                                            ->label('Archivos de Orden de Compra')
                                            ->schema([
                                                FileUpload::make('ruta_archivo')
                                                    ->label('Archivo PDF o JPG')
                                                    ->disk('public')
                                                    ->directory('ordenes_compra')
                                                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg'])
                                                    ->storeFileNamesIn('nombre_archivo')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->downloadable()
                                                    ->openable(),
                                                Hidden::make('nombre_archivo'),
                                                TextInput::make('comentarios')
                                                    ->label('Comentarios / Referencia')
                                                    ->default('Orden de Compra'),
                                                Hidden::make('tipo_documento')->default('Orden de Compra'),
                                            ])
                                            ->deletable(false)
                                            ->disabled(fn (?Requisicion $record) => isset($record) && ! in_array($record->id_estatus, [5, 6, 7, 8]))
                                            ->addActionLabel('Subir Orden de Compra')
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->defaultItems(0)
                                    ])
                            ])
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')->label('Folio')->searchable()->sortable(),
                TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->limit(30)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('solicitante.name')
                    ->label('Solicitante')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('departamento.nombre')
                    ->label('Departamento')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha_creacion')->label('Fecha')->date()->sortable(),
                TextColumn::make('estatus.nombre')
                    ->label('Estatus')
                    ->badge()
                    ->color(function ($record) {
                        $color = \App\Models\Recepcion\Estatus::find($record->id_estatus)?->color;
                        
                        if (!$color) {
                            return 'gray';
                        }

                        if (str_starts_with($color, '#')) {
                            return Color::hex($color);
                        }

                        return $color;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->label(fn (Requisicion $record) => $record->id_estatus == 3 ? 'Cotizar' : 'Subir OC')
                    ->icon(fn (Requisicion $record) => $record->id_estatus == 3 ? null : 'heroicon-o-document-plus')
                    ->color(fn (Requisicion $record) => $record->id_estatus == 3 ? 'warning' : 'success')
                    ->visible(fn (Requisicion $record) => in_array($record->id_estatus, [3, 5])),
                Action::make('marcar_lista_entrega')
                    ->label('Recibido en Oficina')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Recepción')
                    ->modalDescription('¿Confirmas que los materiales ya han llegado a las oficinas de compras y están listos para ser entregados?')
                    ->action(function (Requisicion $record) {
                        $record->id_estatus = 7; // Lista para Entrega
                        $record->save();
                        Notification::make()
                            ->title('Requisición lista para entrega')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Requisicion $record) => $record->id_estatus == 6), // En Proceso de Compra

                Action::make('marcar_completada')
                    ->label('Entregado (Completar)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Entrega Final')
                    ->modalDescription('¿Confirmas que los materiales han sido entregados al solicitante? Esto finalizará el ciclo.')
                    ->action(function (Requisicion $record) {
                        $record->id_estatus = 8; // Completada
                        $record->fecha_entrega = now();
                        $record->save();
                        Notification::make()
                            ->title('Requisición completada')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Requisicion $record) => $record->id_estatus == 7), // Lista para Entrega
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();

        // Si es Gestor de Dirección de Administración (o nombre previo), filtrar las requisiciones asignadas y con los estatus permitidos
        if ($user && in_array($user->rol->nombre, ['Gestor de Administración', 'Gestor de Compras'])) {
            $query->where('id_usuario', $user->id_usuario) // Asignada a este gestor
                  ->whereIn('id_estatus', [3, 4, 5, 6, 7, 8, 9]); // Estatus permitidos
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionCompras::route('/'),
            'create' => Pages\CreateGestionCompras::route('/create'),
            'view' => Pages\ViewGestionCompras::route('/{record}'),
            'edit' => Pages\EditGestionCompras::route('/{record}/edit'),

        ];
    }
}
