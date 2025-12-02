<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use App\Models\Recepcion\Requisicion;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs\Tab;
use App\Models\Recepcion\Estatus;
use Filament\Support\Colors\Color;

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
        $baseQuery = static::getResource()::getEloquentQuery();

        // Helper function to get color safely
        $getColor = function($id) {
             $status = \App\Models\Recepcion\Estatus::find($id);
             $color = $status ? $status->color : 'gray';

             if (!$color) return 'gray';

             if (str_starts_with($color, '#')) {
                 return Color::hex($color);
             }
             
             // Si parece un código hexadecimal pero sin #
             if (preg_match('/^([a-f0-9]{6}|[a-f0-9]{3})$/i', $color)) {
                 return Color::hex('#' . $color);
             }

             return $color;
        };

        return [
            'todos' => Tab::make('Todos')
                ->badge($baseQuery->clone()->count()),
            'Recibida' => Tab::make('Recibida')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 2))
                ->badge($baseQuery->clone()->where('id_estatus', 2)->count())
                ->badgeColor($getColor(2)),
            'cotizacion' => Tab::make('Asignada/En Cotización')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 3))
                ->badge($baseQuery->clone()->where('id_estatus', 3)->count())
                ->badgeColor($getColor(3)),
            'rechazadas' => Tab::make('Rechazadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 9))
                ->badge($baseQuery->clone()->where('id_estatus', 9)->count())
                ->badgeColor($getColor(9)),
        ];
    }
}
