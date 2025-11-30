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
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\DetalleOrdenCompra;
use App\Models\Recepcion\Documento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Compras\GestionCompras\Pages\ViewGestionCompras;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;

use App\Models\Usuarios\Usuario;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use BackedEnum;

use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Auth;

use Filament\Support\Icons\Heroicon;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Colors\Color;

class GestionComprasResource extends Resource
{
    protected static ?string $model = Requisicion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static ?string $navigationLabel = 'Gestión de Compras';
    protected static ?string $slug = 'gestion-compras';
    protected static ?string $modelLabel = 'Requisición para Compra';
    protected static ?string $pluralModelLabel = 'Requisiciones para Compra';


    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->rol->nombre == 'Gestor de Compras' || $user->rol->nombre == 'Administrador';
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
                                        TextInput::make('usuario.name')->label('Solicitante')->disabled(),
                                        TextInput::make('departamento.nombre')->label('Departamento')->disabled(),
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
                                        TextInput::make('nombre_proveedor')
                                            ->label('Nombre del Proveedor')
                                            ->required()
                                            ->columnSpan(2),
                                        DatePicker::make('fecha_cotizacion')
                                            ->label('Fecha de Cotización')
                                            ->default(now())
                                            ->required(),
                                        
                                        Repeater::make('detalles')
                                            ->relationship()
                                            ->label('Ítems a Cotizar')
                                            ->schema([
                                                TextInput::make('descripcion')->label('Descripción')->disabled(),
                                                TextInput::make('unidad_medida')->label('U.M.')->disabled(),
                                                TextInput::make('cantidad_cotizada')->label('Cantidad')->numeric()->disabled(),
                                                TextInput::make('precio_unitario')
                                                    ->label('Precio Unitario')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, $set, $get) {
                                                        $cantidad = $get('cantidad_cotizada') ?? 0;
                                                        $set('subtotal', round($state * $cantidad, 2));
                                                    })
                                                    ->required(),
                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->disabled()
                                                    ->dehydrated()
                                            ])
                                            ->columns(5)
                                            ->addable(false)
                                            ->deletable(false)
                                            ->visible(fn ($record) => $record->requisicion && $record->requisicion->detalles()->exists())
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

                                // Columna Derecha: Carga de Cotizaciones (Nuevos Documentos)
                                Section::make('Cargar Cotización')
                                    ->columnSpan(1)
                                    ->description('Suba aquí el PDF de la cotización recibida del proveedor.')
                                    ->schema([
                                        // Usamos un Repeater filtrado solo para Cotizaciones.
                                        // Filament manejará la creación de nuevos registros con este tipo automáticamente.
                                        // Al estar filtrado, no mostrará ni afectará a los de tipo 'Requisición'.
                                        Repeater::make('documentos_cotizacion')
                                            ->relationship('documentos', fn ($query) => $query->where('tipo_documento', 'Cotización'))
                                            ->label('Archivos de Cotización')
                                            ->schema([
                                                FileUpload::make('ruta_archivo')
                                                    ->label('Archivo PDF')
                                                    ->disk('public')
                                                    ->directory('cotizaciones')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->storeFileNamesIn('nombre_archivo')
                                                    ->required()
                                                    ->columnSpanFull()
                                                    ->downloadable()
                                                    ->openable(),
                                                
                                                // Campo oculto o readonly para nombre_archivo no es necesario si solo se usa internamente,
                                                // pero Filament necesita saber que existe en el schema si se va a usar en storeFileNamesIn y validaciones.
                                                // Lo dejamos Hidden para que no moleste en la UI pero exista en el state.
                                                Hidden::make('nombre_archivo'),

                                                TextInput::make('comentarios')
                                                     ->label('Comentarios / Referencia')
                                                     ->default('Cotización Proveedor'),


                                                Hidden::make('tipo_documento')->default('Cotización'),
                                            ])
                                            ->addActionLabel('Agregar otra cotización')
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->defaultItems(1)
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
                TextColumn::make('concepto')->label('Concepto')->searchable()->limit(30),
                TextColumn::make('usuario.name')->label('Solicitante'),
                TextColumn::make('departamento.nombre')->label('Departamento'),
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
                    ->label('Cotizar')
                    ->visible(fn (Requisicion $record) => $record->id_estatus == 3),
                Action::make('generar_oc')
                    ->label('Generar OC')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->url(fn (Requisicion $record): string => Pages\GenerarOrdenCompra::getUrl(['requisicion_id' => $record->id_requisicion]))
                    ->visible(fn (Requisicion $record) => $record->id_estatus == 5) // Solo si Aprobada? (Asumo 5 es aprobada)
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();

        // Si es Gestor de Compras, filtrar las requisiciones asignadas y con los estatus permitidos
        if ($user && $user->rol->nombre == 'Gestor de Compras') {
            $query->where('id_usuario', $user->id_usuario) // Asignada a este gestor
                  ->whereIn('id_estatus', [3, 4, 5]); // Estatus permitidos
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
            'generar-orden' => Pages\GenerarOrdenCompra::route('/generar-orden'),
        ];
    }
}
