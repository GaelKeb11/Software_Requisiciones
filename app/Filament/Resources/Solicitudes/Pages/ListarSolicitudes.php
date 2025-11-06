<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListarSolicitudes extends ListRecords
{
    protected static string $resource = SolicitudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
