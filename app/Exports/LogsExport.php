<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LogsExport implements FromArray, WithHeadings
{
    protected array $logs;

    public function __construct(array $logs)
    {
        $this->logs = $logs;
    }

    public function array(): array
    {
        // Convert simple string array to array of arrays for Excel
        return array_map(function ($log) {
            return [$log];
        }, $this->logs);
    }

    public function headings(): array
    {
        return [
            'Log Entry',
        ];
    }
}

