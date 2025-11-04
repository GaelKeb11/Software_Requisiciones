<?php

namespace App\Filament\Resources\Solicitudes;

use App\Filament\Resources\Solicitudes\Pages\CrearSolicitud;
use App\Filament\Resources\Solicitudes\Pages\EditarSolicitud;
use App\Filament\Resources\Solicitudes\Pages\ListarSolicitudes;
use App\Filament\Resources\Solicitudes\Schemas\FormularioSolicitud;
use App\Filament\Resources\Solicitudes\Tables\TablaSolicitudes;
use App\Models\Recepcion\Requisicion;
use BackedEnum;
use Filament\Forms\Form; // <-- CAMBIO 1: Se importa la clase Form
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\Solicitudes\RelationManagers\EstatusRelationManager;



class SolicitudResource extends Resource
{
    protected static ?string $model = Requisicion::class;
    protected static ?string $navigationLabel = 'Mis Solicitudes';
    protected static ?string $modelLabel = 'Solicitud de Requisición';
    protected static ?string $pluralModelLabel = 'Mis Solicitudes';
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
        return FormularioSolicitud::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TablaSolicitudes::configure($table);
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
            'index' => ListarSolicitudes::route('/'),
            'create' => CrearSolicitud::route('/create'),
            'edit' => EditarSolicitud::route('/{record}/edit'),
        ];
    }
}
