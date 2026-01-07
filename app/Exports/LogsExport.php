<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogsExport implements FromArray, WithHeadings
{
    protected array $logs;

    public function __construct(iterable $logs)
    {
        $this->logs = collect($logs)
            ->map(function ($log) {
                $props = $log['properties'] ?? null;

                if (is_array($props) || is_object($props)) {
                    $props = json_encode($props, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                }

                return [
                    'id' => $log['id'] ?? null,
                    'log_name' => $log['log_name'] ?? null,
                    'description' => $log['description'] ?? null,
                    'subject_type' => $log['subject_type'] ?? null,
                    'event' => $log['event'] ?? null,
                    'subject_id' => $log['subject_id'] ?? null,
                    'causer_type' => $log['causer_type'] ?? null,
                    'causer_id' => $log['causer_id'] ?? null,
                    'properties' => $props,
                    'batch_uuid' => $log['batch_uuid'] ?? null,
                    'created_at' => $log['created_at'] ?? null,
                    'updated_at' => $log['updated_at'] ?? null,
                ];
            })
            ->all();
    }

    public function array(): array
    {
        return $this->logs;
    }

    public function headings(): array
    {
        return [
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
        ];
    }
}

