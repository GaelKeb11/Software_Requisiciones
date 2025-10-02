<?php

namespace App\Filament\Resources\Usuarios;

use App\Filament\Resources\Usuarios\Pages\CreateUsuarios;
use App\Filament\Resources\Usuarios\Pages\EditUsuarios;
use App\Filament\Resources\Usuarios\Pages\ListUsuarios;
use App\Filament\Resources\Usuarios\Pages\ViewUsuarios;
use App\Filament\Resources\Usuarios\Schemas\UsuariosForm;
use App\Filament\Resources\Usuarios\Schemas\UsuariosInfolist;
use App\Filament\Resources\Usuarios\Tables\UsuariosTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Models\Usuarios\User;
use Filament\Tables\Columns\TextColumn;

class UsuariosResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id_usuario';

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
        return UsuariosTable::configure($table)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('email'),
                TextColumn::make('roles.nombre'),
                TextColumn::make('departamento.nombre'),

            ]);
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
