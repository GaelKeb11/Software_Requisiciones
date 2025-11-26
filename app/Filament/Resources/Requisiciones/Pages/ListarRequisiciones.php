<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class ListarRequisiciones extends ListRecords
{
    protected static string $resource = RequisicionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // Obtenemos el query base del recurso, que ya filtra los borradores si es Recepcionista
        $baseQuery = static::getResource()::getEloquentQuery();

        return [
            'todos' => Tab::make('Todos')
                ->badge($baseQuery->clone()->count()),
            'Recibida' => Tab::make('Recibida')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 2))
                ->badge($baseQuery->clone()->where('id_estatus', 2)->count()),
            'cotizacion' => Tab::make('Asignada/En Cotización')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 3))
                ->badge($baseQuery->clone()->where('id_estatus', 3)->count()),
            'rechazadas' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 6))
                ->badge($baseQuery->clone()->where('id_estatus', 6)->count()),
        ];
    }
}
