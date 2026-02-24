<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recepcion\Estatus;

class EstatusSeeder extends Seeder
{
    public function run(): void
    {
        $estatus = [
            [1, 'Borrador', '#6B7280'],
            [2, 'Recibida', '#3B82F6'],
            [3, 'Asignada / En Cotización', '#EAB308'],
            [4, 'Pendiente de Aprobación', '#EA580C'],
            [5, 'Aprobada (Listo para OC)', '#22C55E'],
            [6, 'En Proceso de Compra', '#9333EA'],
            [7, 'Lista para Entrega', '#0D9488'],
            [8, 'Completada', '#15803D'],
            [9, 'Rechazada', '#EF4444'],
        ];

        foreach ($estatus as $item) {
            Estatus::updateOrCreate(
                ['id_estatus' => $item[0]], // Busca por ID para evitar duplicados
                [
                    'nombre' => $item[1],
                    'color'  => $item[2],
                ]
            );
        }
    }
}