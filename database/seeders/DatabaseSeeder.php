<?php

namespace Database\Seeders;

use App\Models\Usuarios\User;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       // User::factory()->create([
            //'name' => 'Test User',
            //'email' => 'test@example.com',
        //]);

        \App\Models\Recepcion\Departamento::factory(5)->create();
        \App\Models\Recepcion\Clasificacion::factory(3)->create();
        \App\Models\Recepcion\Estatus::factory()->create(['nombre' => 'Recepcionada']);
        \App\Models\Recepcion\Estatus::factory()->create(['nombre' => 'Asignada']);
        \App\Models\Recepcion\Requisicion::factory(20)->create();

        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'id_departamento' => 1, // <-- Nuevo campo
            'id_rol' => 1,          // <-- Nuevo campo
        ]);
    }
}
