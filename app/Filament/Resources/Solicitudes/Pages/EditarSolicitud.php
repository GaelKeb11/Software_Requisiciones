<?php

namespace App\Filament\Resources\Solicitudes\Pages;

use App\Filament\Resources\Solicitudes\SolicitudResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditarSolicitud extends EditRecord
{
    protected static string $resource = SolicitudResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
