<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Relaciones opcionales con departamentos y roles para los usuarios
            if (!Schema::hasColumn('users', 'id_departamento')) {
                $table->foreignId('id_departamento')
                    ->nullable()
                    ->after('remember_token')
                    ->constrained('departamentos', 'id_departamento')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('users', 'id_rol')) {
                $table->foreignId('id_rol')
                    ->nullable()
                    ->after('id_departamento')
                    ->constrained('roles', 'id_rol')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_rol')) {
                $table->dropForeign(['id_rol']);
                $table->dropColumn('id_rol');
            }

            if (Schema::hasColumn('users', 'id_departamento')) {
                $table->dropForeign(['id_departamento']);
                $table->dropColumn('id_departamento');
            }
        });
    }
};
