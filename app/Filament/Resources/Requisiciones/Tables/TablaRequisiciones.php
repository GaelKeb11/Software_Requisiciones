<?php

namespace App\Filament\Resources\Requisiciones\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;

class TablaRequisiciones
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('folio')->label('Folio'),
                TextColumn::make('concepto')->label('Concepto')->limit(30),
                TextColumn::make('departamento.nombre')->label('Departamento'),
                TextColumn::make('clasificacion.nombre')->label('Clasificación'),
                TextColumn::make('usuario.name')->label('Asignado a'),
                TextColumn::make('estatus.nombre')
                    ->label('Estatus')
                    ->badge()
                    ->color(function ($record) {
                        $status = \App\Models\Recepcion\Estatus::find($record->id_estatus);
                        $color = $status?->color;
                        
                        if (!$color) {
                            return 'gray';
                        }

                        // Si es un código hexadecimal con #
                        if (str_starts_with($color, '#')) {
                            return Color::hex($color);
                        }
                        
                        // Si parece un código hexadecimal pero sin #
                        if (preg_match('/^([a-f0-9]{6}|[a-f0-9]{3})$/i', $color)) {
                            return Color::hex('#' . $color);
                        }

                        return $color;
                    }),
            ])
            ->filters([
                // Los filtros se han movido a la página de listado (ListarRequisiciones.php) como Tabs.
            ])
            ->filtersFormColumns(3)
            ->recordActions([
                ViewAction::make(),
                Action::make('asignar')
                    ->label('Asignar Encargado')
                    ->icon('heroicon-o-user-plus')
                    ->url(fn (Model $record): string => RequisicionResource::getUrl('asignar', ['record' => $record]))
                    ->visible(fn (Model $record) => $record->id_usuario === null),
                Action::make('rechazar')
                    ->label('Rechazar')
                    ->icon('heroicon-o-x-circle')
                    ->action(function (Model $record) {
                        $record->update(['id_estatus' => 6]);
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(fn (Model $record) => $record->id_usuario === null),
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
