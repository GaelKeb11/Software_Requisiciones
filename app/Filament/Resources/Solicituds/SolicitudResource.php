<?php

namespace App\Filament\Resources\Solicituds;

use App\Filament\Resources\Solicituds\Pages\CreateSolicitud;
use App\Filament\Resources\Solicituds\Pages\EditSolicitud;
use App\Filament\Resources\Solicituds\Pages\ListSolicituds;
use App\Filament\Resources\Solicituds\Schemas\SolicitudForm;
use App\Filament\Resources\Solicituds\Tables\SolicitudsTable;
use App\Models\Recepcion\Requisicion;
use BackedEnum;
use Filament\Forms\Form; // <-- CAMBIO 1: Se importa la clase Form
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\Solicituds\RelationManagers\EstatusRelationManager;



class SolicitudResource extends Resource
{
    protected static ?string $model = Requisicion::class;
    protected static ?string $navigationLabel = 'Mis Solicitudes';
    protected static ?string $modelLabel = 'Solicitud de Requisición';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'folio';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user->rol->nombre === 'Solicitante' || $user->rol->nombre === 'Administrador';
    }

    // CAMBIO 2: La firma del método ahora usa Form
    public static function form(Schema $form): Schema
    {
        return SolicitudForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SolicitudsTable::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();
        return parent::getEloquentQuery()
            ->where('id_departamento', $user ? $user->id_departamento : 0);

    }

    public static function getRelations(): array
    {
        
            return [
            
            ];
        
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSolicituds::route('/'),
            'create' => CreateSolicitud::route('/create'),
            'edit' => EditSolicitud::route('/{record}/edit'),
        ];
    }
}
