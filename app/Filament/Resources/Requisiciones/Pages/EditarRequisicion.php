<?php

namespace App\Filament\Resources\Requisiciones\Pages;

use App\Filament\Resources\Requisiciones\RequisicionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditarRequisicion extends EditRecord
{
    protected static string $resource = RequisicionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
