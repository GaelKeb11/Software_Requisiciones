<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id('id_requisicion');
            $table->string('folio')->unique();
            $table->date('fecha_creacion');
            $table->date('fecha_recepcion');
            $table->time('hora_recepcion');
            $table->text('concepto');
            
            // Foreign keys
            $table->foreignId('id_departamento')->constrained('departamentos', 'id_departamento');
            $table->foreignId('id_clasificacion')->constrained('clasificaciones', 'id_clasificacion');
            $table->foreignId('id_usuario')->constrained('users');
            $table->foreignId('id_estatus')->constrained('estatus', 'id_estatus');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisiciones');
    }
};