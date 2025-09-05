<?php

namespace App\Filament\Resources\Requisicions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class RequisicionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('folio')->label('Folio'),
                \Filament\Tables\Columns\TextColumn::make('fecha_creacion')->label('Fecha de Creación'),
                \Filament\Tables\Columns\TextColumn::make('fecha_recepcion')->label('Fecha de Recepción'),
                \Filament\Tables\Columns\TextColumn::make('hora_recepcion')->label('Hora de Recepción'),
                \Filament\Tables\Columns\TextColumn::make('concepto')->label('Concepto')->limit(30),
                \Filament\Tables\Columns\TextColumn::make('departamento.nombre')->label('Departamento'),
                \Filament\Tables\Columns\TextColumn::make('clasificacion.nombre')->label('Clasificación'),
                \Filament\Tables\Columns\TextColumn::make('usuario.name')->label('Asignado a'),
                \Filament\Tables\Columns\TextColumn::make('estatus.nombre')->label('Estatus'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
