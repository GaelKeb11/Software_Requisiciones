<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Colors\Color;

class ListarSolicitudes extends ListRecords
{
    protected static string $resource = SolicitudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
            'borrador' => Tab::make('Borrador')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 1))
                ->badge($baseQuery->clone()->where('id_estatus', 1)->count())
                ->badgeColor($getColor(1)),
            'recibida' => Tab::make('Recibida')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 2))
                ->badge($baseQuery->clone()->where('id_estatus', 2)->count())
                ->badgeColor($getColor(2)),
            'cotizacion' => Tab::make('En Cotización')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 3))
                ->badge($baseQuery->clone()->where('id_estatus', 3)->count())
                ->badgeColor($getColor(3)),
            'aprobacion' => Tab::make('Por Aprobar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 4))
                ->badge($baseQuery->clone()->where('id_estatus', 4)->count())
                ->badgeColor($getColor(4)),
            'aprobada' => Tab::make('Aprobada')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 5))
                ->badge($baseQuery->clone()->where('id_estatus', 5)->count())
                ->badgeColor($getColor(5)),
            'rechazada' => Tab::make('Rechazada')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 9))
                ->badge($baseQuery->clone()->where('id_estatus', 9)->count())
                ->badgeColor($getColor(9)),
        ];
    }
}
