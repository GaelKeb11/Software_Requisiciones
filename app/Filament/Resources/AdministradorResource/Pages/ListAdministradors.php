<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdministradors extends ListRecords
{
    protected static string $resource = AdministradorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('logs')
                ->label('Ver Logs del Sistema')
                ->icon('heroicon-o-document-text')
                ->url(AdministradorResource::getUrl('logs'))
                ->color('info'),
            Actions\CreateAction::make(),
        ];
    }
}
