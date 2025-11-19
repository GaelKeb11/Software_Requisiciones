<?php

namespace App\Filament\Resources\Compras\GestionCompras;

use App\Filament\Resources\Compras\GestionCompras\Pages;
use App\Models\Recepcion\Requisicion;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use App\Models\Compras\OrdenCompra;
use App\Models\Compras\DetalleOrdenCompra;
use App\Models\Recepcion\Documento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Forms\Get;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

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
        return $user->esGestorDeCompras() || $user->esAdministrador();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')->label('Folio')->searchable()->sortable(),
                TextColumn::make('concepto')->label('Concepto')->searchable(),
                TextColumn::make('solicitante.name')->label('Solicitante'),
                TextColumn::make('departamento.nombre')->label('Departamento'),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make('Gestionar')
                    ->form(fn(Requisicion $record) => [
                        Tabs::make('Tabs')
                            ->tabs([
                                Tabs\Tab::make('Generar Orden de Compra Digital')
                                    ->schema([
                                        TextInput::make('nombre_proveedor')
                                            ->label('Nombre del Proveedor')
                                            ->required(fn ($get) => empty($get('documento_escaneado'))),
                                        Repeater::make('detalles')
                                            ->label('Artículos de la Requisición')
                                            ->schema([
                                                Hidden::make('id_detalle_requisicion'),
                                                TextInput::make('cantidad')
                                                    ->label('Cantidad')
                                                    ->disabled()
                                                    ->numeric(),
                                                TextInput::make('unidad_medida')
                                                    ->label('Unidad de Medida')
                                                    ->disabled(),
                                                TextInput::make('descripcion')
                                                    ->label('Descripción')
                                                    ->disabled(),
                                                TextInput::make('precio_unitario')
                                                    ->label('Precio Unitario')
                                                    ->required(fn ($get) => !empty($get('nombre_proveedor')))
                                                    ->numeric()
                                                    ->prefix('$'),
                                            ])
                                            ->default(function () use ($record) {
                                                return $record->detalles->map(fn ($detalle) => [
                                                    'id_detalle_requisicion' => $detalle->id_detalle_requisicion,
                                                    'cantidad' => $detalle->cantidad,
                                                    'unidad_medida' => $detalle->unidad_medida,
                                                    'descripcion' => $detalle->descripcion,
                                                ])->toArray();
                                            })
                                            ->columns(4)
                                            ->addable(false)
                                            ->deletable(false)
                                            ->reorderable(false),
                                    ]),
                                Tabs\Tab::make('Subir Documento Escaneado')
                                    ->schema([
                                        FileUpload::make('documento_escaneado')
                                            ->label('Orden de Compra Escaneada')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->disk('public')
                                            ->directory('ordenes-compra-escaneadas')
                                            ->required(fn ($get) => empty($get('nombre_proveedor'))),
                                    ]),
                            ])
                    ])
                    ->action(function (array $data, Requisicion $record) {
                        try {
                            DB::beginTransaction();

                            // Lógica para la Pestaña 1: Orden de Compra Digital
                            if (!empty($data['nombre_proveedor'])) {
                                $totalCalculado = collect($data['detalles'])->reduce(function ($carry, $item) {
                                    return $carry + ($item['cantidad'] * $item['precio_unitario']);
                                }, 0);

                                $ordenCompra = OrdenCompra::create([
                                    'id_requisicion' => $record->id_requisicion,
                                    'nombre_proveedor' => $data['nombre_proveedor'],
                                    'fecha_orden' => Carbon::now(),
                                    'total_calculado' => $totalCalculado,
                                    'id_usuario_gestor' => Auth::id(),
                                ]);

                                foreach ($data['detalles'] as $detalle) {
                                    DetalleOrdenCompra::create([
                                        'id_orden_compra' => $ordenCompra->id_orden_compra,
                                        'id_detalle_requisicion' => $detalle['id_detalle_requisicion'],
                                        'precio_unitario' => $detalle['precio_unitario'],
                                        'subtotal' => $detalle['cantidad'] * $detalle['precio_unitario'],
                                    ]);
                                }
                            }

                            // Lógica para la Pestaña 2: Documento Escaneado
                            if (!empty($data['documento_escaneado'])) {
                                Documento::create([
                                    'id_requisicion' => $record->id_requisicion,
                                    'tipo_documento' => 'Orden de Compra',
                                    'nombre_archivo' => $data['documento_escaneado'],
                                    'ruta_archivo' => $data['documento_escaneado'],
                                    'comentarios' => 'Orden de compra escaneada.',
                                ]);
                            }

                            // Tarea 4: Cambiar estatus de la requisición
                            $record->id_estatus = 2; // "En Revisión"
                            $record->save();

                            DB::commit();
                            Notification::make()
                                ->title('Éxito')
                                ->body('La requisición ha sido procesada y enviada a revisión.')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Error')
                                ->body('Ocurrió un error al procesar la requisición: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalWidth('7xl')
                    ->slideOver(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_estatus', 4) // "En cotización"
            ->where('id_usuario', Auth::id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionCompras::route('/'),
        ];
    }
}
