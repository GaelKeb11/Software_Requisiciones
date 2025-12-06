<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;

class ListGestionCompras extends ListRecords
{
    protected static string $resource = 'App\Filament\Resources\Compras\GestionCompras\GestionComprasResource';

    protected function getHeaderActions(): array
    {
        return [
    
        ];
    }

    public function getTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        $getColor = function($id) {
             $status = \App\Models\Recepcion\Estatus::find($id);
             $color = $status ? $status->color : 'gray';

             if (!$color) return 'gray';

             if (str_starts_with($color, '#')) {
                 return Color::hex($color);
             }
             
             if (preg_match('/^([a-f0-9]{6}|[a-f0-9]{3})$/i', $color)) {
                 return Color::hex('#' . $color);
             }

             return $color;
        };

        return [
            'todos' => Tab::make('Todos')
                ->badge($baseQuery->clone()->count()),
            'cotizacion' => Tab::make('Asignada/En Cotización')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 3))
                ->badge($baseQuery->clone()->where('id_estatus', 3)->count())
                ->badgeColor($getColor(3)),
            'Pendientes de Aprobación' => Tab::make('Pendientes de Aprobación')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 4))
                ->badge($baseQuery->clone()->where('id_estatus', 4)->count())
                ->badgeColor($getColor(4)),
            'Aprobadas' => Tab::make('Aprobadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 5))
                ->badge($baseQuery->clone()->where('id_estatus', 5)->count())
                ->badgeColor($getColor(5)),
            'En Proceso' => Tab::make('En Proceso')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 6))
                ->badge($baseQuery->clone()->where('id_estatus', 6)->count())
                ->badgeColor($getColor(6)),
            'Lista para Entrega' => Tab::make('Lista para Entrega')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 7))
                ->badge($baseQuery->clone()->where('id_estatus', 7)->count())
                ->badgeColor($getColor(7)),
            'Completadas' => Tab::make('Completadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 8))
                ->badge($baseQuery->clone()->where('id_estatus', 8)->count())
                ->badgeColor($getColor(8)),
            'Rechazadas' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 9))
                ->badge($baseQuery->clone()->where('id_estatus', 9)->count())
                ->badgeColor($getColor(9)),
        ];
    }
}
