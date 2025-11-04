<?php

namespace App\Filament\Resources\Requisicions;

use App\Filament\Resources\Requisicions\Pages\CreateRequisicion;
use App\Filament\Resources\Requisicions\Pages\EditRequisicion;
use App\Filament\Resources\Requisicions\Pages\ListRequisicions;
use App\Filament\Resources\Requisicions\Schemas\RequisicionForm;
use App\Filament\Resources\Requisicions\Tables\RequisicionsTable;
use App\Models\Recepcion\Requisicion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;


class RequisicionResource extends Resource
{
    protected static ?string $model = Requisicion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'App\Models\Recepcion\Requisicion';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        // Llama al método hasRole() del modelo User para verificar los permisos.
        return $user->rol->nombre == 'Administrador' || $user->rol->nombre == 'Recepcionista';
    }
    public static function form(Schema $schema): Schema
    {
        return RequisicionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequisicionsTable::configure($table);
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
            'index' => ListRequisicions::route('/'),
            'create' => CreateRequisicion::route('/create'),
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
