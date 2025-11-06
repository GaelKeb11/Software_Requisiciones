<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id('id_departamento');
            $table->string('nombre', 100);
            $table->string('responsable', 100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};