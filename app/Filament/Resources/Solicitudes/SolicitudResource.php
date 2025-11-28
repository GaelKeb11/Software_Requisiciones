<?php

namespace App\Filament\Resources\Solicitudes;

use App\Filament\Resources\Solicitudes\Pages\CrearSolicitud;
use App\Filament\Resources\Solicitudes\Pages\EditarSolicitud;
use App\Filament\Resources\Solicitudes\Pages\ViewSolicitud;
use App\Filament\Resources\Solicitudes\Pages\ListarSolicitudes;
use App\Filament\Resources\Solicitudes\Schemas\FormularioSolicitud;
use App\Filament\Resources\Solicitudes\Tables\TablaSolicitudes;
use App\Models\Recepcion\Requisicion;
use BackedEnum;

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
        return $user && (
            ($user->rol && $user->rol->nombre === 'Solicitante') ||
            ($user->rol && $user->rol->nombre === 'Administrador')
        );
    }


    public static function form(Schema $schema): Schema
    {
        return FormularioSolicitud::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TablaSolicitudes::configure($table);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();
        $query = parent::getEloquentQuery();

        if ($user && $user->rol->nombre === 'Solicitante') {
            return $query->where('id_solicitante', $user->id_usuario);
        }

        return $query;
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
            'view' => ViewSolicitud::route('/{record}'),
        ];
    }
}
