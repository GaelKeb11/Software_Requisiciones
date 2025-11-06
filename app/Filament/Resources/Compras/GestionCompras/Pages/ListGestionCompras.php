<?php

namespace App\Filament\Resources\Compras\GestionCompras\Pages;

use App\Filament\Resources\Compras\GestionCompras\GestionComprasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGestionCompras extends ListRecords
{
    protected static string $resource = GestionComprasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
