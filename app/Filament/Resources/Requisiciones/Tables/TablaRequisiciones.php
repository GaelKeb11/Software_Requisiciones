<?php

namespace App\Filament\Resources\Requisicions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Requisicions\RequisicionResource;
use Filament\Tables\Columns\TextColumn;

class RequisicionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')->label('Folio'),
                TextColumn::make('fecha_recepcion')->label('Fecha de Recepción'),
                TextColumn::make('concepto')->label('Concepto')->limit(30),
                TextColumn::make('departamento.nombre')->label('Departamento'),
                TextColumn::make('clasificacion.nombre')->label('Clasificación'),
                TextColumn::make('usuario.name')->label('Asignado a'),
                TextColumn::make('estatus.nombre')->label('Estatus'),
            ])
            ->filters([
                // Tabs de conteo (se muestran arriba)
                Filter::make('pendientes')
                    ->label(fn () => 'Pendientes (' . \App\Models\Recepcion\Requisicion::where('id_estatus', 1)->count() . ')')
                    ->query(fn (Builder $query) => $query->where('id_estatus', 1))
                    ->default(),

                Filter::make('cotizacion')
                    ->label(fn () => 'En Cotización (' . \App\Models\Recepcion\Requisicion::where('id_estatus', 4)->count() . ')')
                    ->query(fn (Builder $query) => $query->where('id_estatus', 4)),

                Filter::make('rechazadas')
                    ->label(fn () => 'Rechazadas (' . \App\Models\Recepcion\Requisicion::where('id_estatus', 6)->count() . ')')
                    ->query(fn (Builder $query) => $query->where('id_estatus', 6)),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->recordActions([
                Action::make('asignar')
                    ->label('Asignar Encargado')
                    ->icon('heroicon-o-user-plus')
                    ->url(fn (Model $record): string => RequisicionResource::getUrl('asignar', ['record' => $record])),
                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (Model $record) {
                        $record->update(['id_estatus' => 6]);
                    })
                    ->requiresConfirmation()
                    ->color('danger'),
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
