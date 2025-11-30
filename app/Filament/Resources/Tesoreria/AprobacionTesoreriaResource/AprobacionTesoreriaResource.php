<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource;

use App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Pages;
use UnitEnum;
use BackedEnum;
use App\Models\Recepcion\Requisicion;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Colors\Color;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\ViewAction;

class AprobacionTesoreriaResource extends Resource
{
    protected static ?string $model = Requisicion::class;

    protected static ?string $navigationLabel = 'Aprobación Tesorería';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $slug = 'aprobacion-tesoreria';
    protected static ?string $modelLabel = 'Requisición';
    protected static ?string $pluralModelLabel = 'Requisiciones por Aprobar';
    protected static string|UnitEnum|null $navigationGroup = 'Tesorería';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->rol->nombre == 'Tesorería' || $user->rol->nombre == 'Administrador';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Revisión de Requisición')
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
                                        TextInput::make('estatus.nombre')->label('Estatus')->disabled(),
                                    ])
                            ]),

                        // TAB 2: COTIZACIÓN Y DETALLES
                        Tab::make('Cotización')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Detalles Cotizados')
                                    ->schema([
                                        Repeater::make('cotizaciones')
                                            ->relationship()
                                            ->label('Cotización')
                                            ->schema([
                                                TextInput::make('nombre_proveedor')->label('Proveedor')->disabled(),
                                                DatePicker::make('fecha_cotizacion')->label('Fecha')->disabled(),
                                                TextInput::make('total_cotizado')->label('Total')->prefix('$')->disabled(),

                                                Repeater::make('detalles')
                                                    ->relationship()
                                                    ->label('Ítems Cotizados')
                                                    ->schema([
                                                        TextInput::make('descripcion')->label('Descripción')->disabled(),
                                                        TextInput::make('unidad_medida')->label('U.M.')->disabled(),
                                                        TextInput::make('cantidad_cotizada')->label('Cantidad')->disabled(),
                                                        TextInput::make('precio_unitario')->label('Precio Unitario')->prefix('$')->disabled(),
                                                        TextInput::make('subtotal')->label('Subtotal')->prefix('$')->disabled(),
                                                    ])
                                                    ->columns(5)
                                                    ->addable(false)
                                                    ->deletable(false)
                                            ])
                                            ->addable(false)
                                            ->deletable(false)
                                    ]),
                                
                                Section::make('Archivo de Cotización')
                                    ->schema([
                                         Placeholder::make('archivo_cotizacion')
                                            ->label('')
                                            ->content(fn (Requisicion $record) => new HtmlString(
                                                collect($record->documentos->where('tipo_documento', 'Cotización'))->map(function($doc) {
                                                    $url = Storage::url($doc->ruta_archivo);
                                                    return "
                                                        <div class='mb-4 p-2 border rounded'>
                                                            <p class='font-bold text-sm mb-2'>Documento: {$doc->nombre_archivo}</p>
                                                            <p class='text-sm mb-2 italic'>{$doc->comentarios}</p>
                                                            <iframe src='{$url}' width='100%' height='500px' style='border: none;'></iframe>
                                                            <div class='mt-2 text-right'>
                                                                <a href='{$url}' target='_blank' class='text-primary-600 hover:underline text-sm'>Descargar / Abrir en nueva pestaña</a>
                                                            </div>
                                                        </div>
                                                    ";
                                                })->join('') ?: '<p class="text-gray-500 italic">No se ha cargado archivo de cotización.</p>'
                                            ))
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
                        $color = $record->estatus?->color;

                        if (!$color) {
                            return 'gray';
                        }

                        if (str_starts_with($color, '#')) {
                            return Color::hex($color);
                        }

                        if (preg_match('/^([a-f0-9]{6}|[a-f0-9]{3})$/i', $color)) {
                            return Color::hex('#' . $color);
                        }

                        return $color;
                    }),
            ])
            ->filters([])
            ->actions([
                ViewAction::make()->label('Revisar'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAprobacionTesoreria::route('/'),
            'view' => Pages\ViewAprobacionTesoreria::route('/{record}'),
        ];
    }
}
