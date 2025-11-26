<?php

namespace App\Filament\Resources\Compras\GestionCompras;

use App\Filament\Resources\Compras\GestionCompras\Pages;
use App\Models\Recepcion\Requisicion;

use Filament\Resources\Resource;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

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

use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use filament\users\Usuario;
use Filament\Actions\Action;



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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')->label('Folio')->searchable()->sortable(),
                TextColumn::make('concepto')->label('Concepto')->searchable(),
                TextColumn::make('usuario.name')->label('Solicitante'),
                TextColumn::make('departamento.nombre')->label('Departamento'),
                TextColumn::make('fecha_creacion')->label('Fecha de Creación')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('generar_oc')
                    ->label('Generar OC')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->url(fn (Requisicion $record): string => Pages\GenerarOrdenCompra::getUrl(['requisicion_id' => $record->id_requisicion]))
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('id_estatus', 4);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionCompras::route('/'),
            'generar-orden' => Pages\GenerarOrdenCompra::route('/generar-orden'),
        ];
    }
}
