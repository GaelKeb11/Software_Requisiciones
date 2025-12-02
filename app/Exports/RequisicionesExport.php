<?php

namespace App\Exports;

use App\Models\Recepcion\Requisicion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RequisicionesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Requisicion::with(['usuario', 'departamento', 'estatus', 'cotizaciones.detalles'])->get();
    }

    public function headings(): array
    {
        return [
            'Folio',
            'Fecha Creación',
            'Solicitante',
            'Departamento',
            'Estatus',
            'Concepto',
            'Total Estimado',
        ];
    }

    public function map($requisicion): array
    {
        $total = $requisicion->cotizaciones->first()?->detalles->sum('subtotal') ?? 0;

        return [
            $requisicion->folio,
            $requisicion->created_at->format('d/m/Y H:i'),
            $requisicion->usuario->name ?? 'N/A',
            $requisicion->departamento->nombre ?? 'N/A',
            $requisicion->estatus->nombre ?? 'N/A',
            $requisicion->concepto,
            $total,
        ];
    }
}
