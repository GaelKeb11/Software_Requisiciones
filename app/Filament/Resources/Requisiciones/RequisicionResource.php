<?php

namespace App\Filament\Resources\Requisiciones;

use App\Filament\Resources\Requisiciones\Pages\CrearRequisicion;
use App\Filament\Resources\Requisiciones\Pages\EditarRequisicion;
use App\Filament\Resources\Requisiciones\Pages\ListarRequisiciones;
use App\Filament\Resources\Requisiciones\Schemas\FormularioRequisicion;
use App\Filament\Resources\Requisiciones\Tables\TablaRequisiciones;
use App\Models\Recepcion\Requisicion;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Resources\Resource;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;


class RequisicionResource extends Resource
{
    protected static ?string $model = Requisicion::class;

    protected static ?string $navigationLabel = 'Requisiciones';
    protected static ?string $modelLabel = 'Requisición';
    protected static ?string $pluralModelLabel = 'Requisiciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'folio';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->rol->nombre == 'Recepcionista' || $user->rol->nombre == 'Administrador';
    }
    public static function form(Schema $schema): Schema
    {
        return FormularioRequisicion::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TablaRequisiciones::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListarRequisiciones::route('/'),
            'create' => CrearRequisicion::route('/create'),
            'asignar' => Pages\AsignarRequisicion::route('/{record}/asignar'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getFormSchema(): array
    {
        return [
            // Todos los campos deshabilitados por defecto
            TextInput::make('folio')->disabled(),
            DatePicker::make('fecha_creacion')->disabled(),
            DatePicker::make('fecha_recepcion')->disabled(),
            TextInput::make('hora_recepcion')->disabled(),
            Textarea::make('concepto')->disabled(),
        ];
    }
}
