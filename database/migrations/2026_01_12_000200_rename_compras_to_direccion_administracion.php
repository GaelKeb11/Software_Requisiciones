<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('departamentos')
            ->where('nombre', 'Compras')
            ->update(['nombre' => 'Dirección de Administración']);

        DB::table('roles')
            ->where('nombre', 'Gestor de Compras')
            ->update(['nombre' => 'Gestor de Administración']);
    }

    public function down(): void
    {
        DB::table('departamentos')
            ->where('nombre', 'Dirección de Administración')
            ->update(['nombre' => 'Compras']);

        DB::table('roles')
            ->where('nombre', 'Gestor de Administración')
            ->update(['nombre' => 'Gestor de Compras']);
    }
};
