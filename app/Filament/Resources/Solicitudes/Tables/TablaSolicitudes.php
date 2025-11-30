<?php

namespace App\Filament\Resources\Solicitudes\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Colors\Color;

class TablaSolicitudes
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')
                    ->label('Folio')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('concepto')
                    ->label('Concepto')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('departamento.nombre')
                    ->label('Departamento Solicitante')
                    ->sortable(),
                TextColumn::make('estatus.nombre')
                    ->label('Estatus')
                    ->badge()
                    ->color(function ($record) {
                        $color = \App\Models\Recepcion\Estatus::find($record->id_estatus)?->color;
                        
                        if (!$color) {
                            return 'gray';
                        }

                        if (str_starts_with($color, '#')) {
                            return Color::hex($color);
                        }

                        return $color;
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_creacion')
                    ->label('Fecha de Solicitud')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->outlined(),
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->outlined(),
                DeleteAction::make()
                    ->hidden(function ($record) {
                        /** @var \App\Models\Usuarios\Usuario $user */
                        $user = Auth::user();
                        return $record->id_estatus >= 2 && !$user->esAdministrador();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                /** @var \App\Models\Usuarios\Usuario $user */
                $user = Auth::user();
                
                if ($user->esRecepcionista()) {
                    $query->where('id_estatus', 2);
                } else {
                    $query->where('id_departamento', $user->id_departamento);
                }
            });
    }
}
