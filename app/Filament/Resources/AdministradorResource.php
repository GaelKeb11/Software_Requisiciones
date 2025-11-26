<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdministradorResource\Pages;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;
use App\Models\Usuarios\Usuario;
use Illuminate\Support\Facades\Auth;

class AdministradorResource extends Resource
{
    protected static ?string $model = Usuario::class; // Asegúrate de usar tu modelo de usuario
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static ?string $navigationLabel = 'Administradores';
    protected static ?string $modelLabel = 'Administrador';
    protected static ?string $pluralModelLabel = 'Administradores';
    protected static string|UnitEnum|null $navigationGroup = 'Administración';	
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\Usuarios\Usuario $user */
        $user = Auth::user();
        return $user->esAdministrador();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\AdminMenu::route('/'),
            'list' => Pages\ListAdministradors::route('/list'),
            'logs' => Pages\ActivityLog::route('/logs'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            // Agrega aquí relaciones si son necesarias
        ];
    }
}
