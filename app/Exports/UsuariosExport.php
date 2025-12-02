<?php

namespace App\Exports;

use App\Models\Usuarios\Usuario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsuariosExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Usuario::with(['rol', 'departamento'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Email',
            'Rol',
            'Departamento',
            'Fecha Creación',
        ];
    }

    public function map($usuario): array
    {
        return [
            $usuario->id,
            $usuario->name,
            $usuario->email,
            $usuario->rol->nombre ?? 'N/A',
            $usuario->departamento->nombre ?? 'N/A',
            $usuario->created_at->format('d/m/Y H:i'),
        ];
    }
}

