<?php

namespace App\Filament\Resources\Usuarios;

use App\Filament\Resources\Usuarios\Pages\CreateUsuarios;
use App\Filament\Resources\Usuarios\Pages\EditUsuarios;
use App\Filament\Resources\Usuarios\Pages\ListUsuarios;
use App\Filament\Resources\Usuarios\Pages\ViewUsuarios;
use App\Filament\Resources\Usuarios\Schemas\UsuariosForm;
use App\Filament\Resources\Usuarios\Schemas\UsuariosInfolist;
use App\Filament\Resources\Usuarios\Tables\UsuariosTable;
use App\Models\Usuarios\Usuario;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;


class UsuariosResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';

    // Se cambió el ícono para que sea más representativo de los usuarios.
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    // Se ajustó para mostrar el nombre del usuario, que es más legible.
    protected static ?string $recordTitleAttribute = 'nombreCompleto';

    /**
     * Controla la visibilidad del recurso en el panel.
     * Solo los usuarios con el rol 'Administrador' podrán verlo.
     */
    public static function canViewAny(): bool
    {
        // Se utiliza el método del Trait para una verificación más limpia.
        return Auth::user()->esAdministrador();
    }

    public static function form(Schema $schema): Schema
    {
        return UsuariosForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UsuariosInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        // Se corrigió para delegar completamente la configuración a la clase de tabla.
        return UsuariosTable::configure($table);
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
            'index' => ListUsuarios::route('/'),
            'create' => CreateUsuarios::route('/create'),
            'view' => ViewUsuarios::route('/{record}'),
            'edit' => EditUsuarios::route('/{record}/edit'),
        ];
    }
}
