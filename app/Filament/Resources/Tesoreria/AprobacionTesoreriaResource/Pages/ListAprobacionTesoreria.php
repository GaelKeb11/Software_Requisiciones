<?php

namespace App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\Pages;

use App\Filament\Resources\Tesoreria\AprobacionTesoreriaResource\AprobacionTesoreriaResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Filament\Actions\Action;

class ListAprobacionTesoreria extends ListRecords
{
    protected static string $resource = AprobacionTesoreriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reportes')
                ->label('Reportes y Estadísticas')
                ->icon('heroicon-o-presentation-chart-line')
                ->url(AprobacionTesoreriaResource::getUrl('reportes')),
        ];
    }

    // Aseguramos que NO haya widgets aquí
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        // Helper function to get color safely (copied logic to ensure stability)
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
            'pendientes' => Tab::make('Pendiente de Aprobación')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 4))
                ->badge($baseQuery->clone()->where('id_estatus', 4)->count())
                ->badgeColor($getColor(4)),
            
            'aprobadas' => Tab::make('Aprobada (Listo para OC)')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 5))
                ->badge($baseQuery->clone()->where('id_estatus', 5)->count())
                ->badgeColor($getColor(5)),

            'completadas' => Tab::make('Completada')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('estatus', fn ($q) => $q->where('nombre', 'Completada')))
                ->badge($baseQuery->clone()->whereHas('estatus', fn ($q) => $q->where('nombre', 'Completada'))->count())
                ->badgeColor('success'),

            'rechazadas' => Tab::make('Rechazada')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('id_estatus', 9))
                ->badge($baseQuery->clone()->where('id_estatus', 9)->count())
                ->badgeColor($getColor(9)),
        ];
    }
}
