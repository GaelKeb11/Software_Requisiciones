<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuarios\Rol; // Verifica si tu modelo se llama Role o Rol

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id_rol' => 2,
                'nombre' => 'Solicitante',
                'descripcion' => 'Usuario que puede crear y dar seguimiento a sus propias requisiciones.'
            ],
            [
                'id_rol' => 3,
                'nombre' => 'Recepcionista',
                'descripcion' => 'Usuario encargado de recibir y validar las requisiciones iniciales.'
            ],
            [
                'id_rol' => 4,
                'nombre' => 'Gestor de Compras',
                'descripcion' => 'Usuario que gestiona el proceso de compra de las requisiciones aprobadas.'
            ],
            [
                'id_rol' => 6,
                'nombre' => 'Tesorería',
                'descripcion' => 'Usuario encargado de la gestión de pagos y finanzas relacionadas a las compras.'
            ],
            [
                'id_rol' => 7,
                'nombre' => 'Administrador',
                'descripcion' => 'Superusuario con acceso total a todas las funcionalidades del sistema.'
            ],
            [
                'id_rol' => 8,
                'nombre' => 'Director',
                'descripcion' => 'Es el director de una departamento o área de trabajo'
            ],
            [
                'id_rol' => 9,
                'nombre' => 'Administrador Sistema',
                'descripcion' => 'Administrador del sistema'
            ],
        ];

        foreach ($roles as $rol) {
            Rol::updateOrCreate(
                ['id_rol' => $rol['id_rol']],
                [
                    'nombre' => $rol['nombre'],
                    'descripcion' => $rol['descripcion'],
                ]
            );
        }
    }
}