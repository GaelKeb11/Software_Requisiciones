<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido_paterno')->after('name');
            $table->string('apellido_materno')->after('apellido_paterno');
            $table->string('numero_telefonico')->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'apellido_paterno',
                'apellido_materno',
                'numero_telefonico'
            ]);
        });
    }
};
