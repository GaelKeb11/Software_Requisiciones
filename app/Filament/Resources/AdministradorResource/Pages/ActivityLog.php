<?php

namespace App\Filament\Resources\AdministradorResource\Pages;

use App\Filament\Resources\AdministradorResource;
use App\Exports\LogsExport;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Filament\Actions\Action;

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
        // Trae los últimos 200 registros del activity_log con columnas relevantes
        $this->logs = Activity::query()
            ->latest('created_at')
            ->limit(200)
            ->get([
                'id',
                'log_name',
                'description',
                'subject_type',
                'event',
                'subject_id',
                'causer_type',
                'causer_id',
                'properties',
                'batch_uuid',
                'created_at',
                'updated_at',
            ])
            ->map(function (Activity $activity) {
                return [
                    'id' => $activity->id,
                    'log_name' => $activity->log_name,
                    'description' => $activity->description,
                    'subject_type' => $activity->subject_type,
                    'event' => $activity->event,
                    'subject_id' => $activity->subject_id,
                    'causer_type' => $activity->causer_type,
                    'causer_id' => $activity->causer_id,
                    'properties' => $activity->properties,
                    'batch_uuid' => $activity->batch_uuid,
                    'created_at' => optional($activity->created_at)->toDateTimeString(),
                    'updated_at' => optional($activity->updated_at)->toDateTimeString(),
                ];
            })
            ->toArray();
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Exportar a Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    return Excel::download(new LogsExport($this->logs), 'activity_log.xlsx');
                }),
        ];
    }
}
