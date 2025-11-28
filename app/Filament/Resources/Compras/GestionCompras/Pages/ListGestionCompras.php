<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListGestionCompras extends ListRecords
{
    protected static string $resource = 'App\Filament\Resources\Compras\GestionCompras\GestionComprasResource';

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
            'cotizacion' => Tab::make('Asignada/En Cotización')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 3))
                ->badge($baseQuery->clone()->where('id_estatus', 3)->count()),
            'Pendientes de Aprobación' => Tab::make('Pendientes de Aprobación')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 4))
                ->badge($baseQuery->clone()->where('id_estatus', 4)->count()),
            'Aprobadas' => Tab::make('Aprobadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 5))
                ->badge($baseQuery->clone()->where('id_estatus', 5)->count()),
            'Rechazadas' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 6))
                ->badge($baseQuery->clone()->where('id_estatus', 6)->count()),
        ];
    }
}
