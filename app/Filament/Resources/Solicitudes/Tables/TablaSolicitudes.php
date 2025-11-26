<?php

namespace App\Filament\Resources\Solicitudes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


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
                    ->limit(40), // Limita el texto para no saturar la tabla
                TextColumn::make('departamento.nombre') // Usando la relación
                    ->label('Departamento Solicitante')
                    ->sortable(),
                TextColumn::make('estatus.nombre')
                    ->label('Estatus')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_creacion')
                    ->label('Fecha de Solicitud')
                    ->date('d/m/Y')
                    ->sortable(),
            ])//
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
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
