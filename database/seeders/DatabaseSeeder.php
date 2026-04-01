<?php

namespace Database\Seeders;

use App\Models\Recepcion\Requisicion;
use App\Models\Usuarios\Usuario;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Cargamos los catálogos con los datos reales que creamos
        $this->call([
            DepartamentoSeeder::class, // Tus 52 departamentos reales
            EstatusSeeder::class,      // Tus 9 estatus reales
            ClasificacionSeeder::class,
            RolSeeder::class,
        ]);       
        Usuario::create([
            'name' => 'Admin',
            'apellido_paterno' => 'Admin',
            'apellido_materno' => 'Admin',
            'email' => 'gaelkebcauich@gmail.com',
            'password' => bcrypt('12345678'),
            'id_departamento' => 30, // Dirección de Tecnologías de la Información y Conectividad (según tu lista)
            'id_rol' => 7,          // ID de Administrador (según tu SQL)
        ]);
        Usuario::factory(3)->create([
            'id_departamento' => 27,
            'id_rol'=>4,
        ]); // Crea 3
        Usuario::factory(7)->create(); // Crea 7 usuarios más re
        
        //Requisicion::factory(10)->create();
        $this->call([
            RequisicionSeeder::class,
	    DetalleRequisicionSeeder::class,
        ]);      

        

    }
}
