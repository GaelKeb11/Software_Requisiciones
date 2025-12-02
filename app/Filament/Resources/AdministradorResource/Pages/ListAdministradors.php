<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Auth;


class ListAdministradors extends ListRecords
{
    protected static string $resource = AdministradorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backupDatabase')
                ->label('Respaldar Base de Datos')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => Auth::user()->rol->nombre === 'Administrador')
                ->action(function () {
                    try {
                        $relativePath = AdministradorResource::generateDatabaseBackup();
                        $downloadName = basename($relativePath);

                        Notification::make()
                            ->title('Respaldo generado')
                            ->body('El archivo se descargará en breve.')
                            ->success()
                            ->send();

                        return response()->download(storage_path('app/backup/' . $downloadName));
                    } catch (Throwable $th) {
                        Notification::make()
                            ->title('No se pudo generar el respaldo')
                            ->body($th->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('logs')
                ->label('Ver Logs del Sistema')
                ->icon('heroicon-o-document-text')
                ->url(AdministradorResource::getUrl('logs'))
                ->color('info'),
            Actions\CreateAction::make(),
        ];
    }
}
