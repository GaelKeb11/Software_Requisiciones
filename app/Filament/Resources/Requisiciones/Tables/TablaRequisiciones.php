<?php

namespace App\Filament\Resources\Requisiciones\Tables;

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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class TablaRequisiciones
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
                BadgeColumn::make('estatus.nombre')
                    ->label('Estatus')
                    ->color(fn (string $state): string => match ($state) {
                        'Recepcionada' => 'gray',
                        'Pendientes' => 'warning',
                        'Asignada' => 'info',
                        'En Cotización' => 'info',
                        'En Revisión' => 'primary',
                        'Rechazada' => 'danger',
                        'Aprobada' => 'success',
                        'Completada' => 'success',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                // Los filtros se han movido a la página de listado (ListarRequisiciones.php) como Tabs.
            ])
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
