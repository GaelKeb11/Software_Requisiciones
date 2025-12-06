<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogsExport;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios\Usuario;

class ActivityLog extends Page
{
    protected static string $resource = AdministradorResource::class;

    protected static ?string $title = 'Logs del Sistema';

    protected string $view = 'filament.resources.administrador-resource.pages.activity-log';

    public array $logs = [];

    public function mount(): void
    {
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            $content = File::get($logFile);
            // Simple parsing logic, can be improved based on log format
            // Assuming default Laravel log format
            $lines = explode("\n", $content);
            $this->logs = array_filter($lines);
            // Get last 100 lines for display performance
            $this->logs = array_slice(array_reverse($this->logs), 0, 100);
        }
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new LogsExport($this->logs), 'system_logs.xlsx');
                }),
        ];
    }
}
