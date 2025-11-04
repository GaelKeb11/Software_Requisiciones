<?php

namespace App\Filament\Resources\Solicituds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class SolicitudsTable
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('id_departamento', auth::user()->id_departamento);
            });
    }
}
