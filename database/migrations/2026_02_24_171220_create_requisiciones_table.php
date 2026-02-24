<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requisiciones', function (Blueprint $table) {
            $table->id('id_requisicion');
            $table->string('folio')->unique();
            
            // RELACIÓN CON DEPARTAMENTOS
            // Definimos la columna primero y luego la relación
            $table->unsignedBigInteger('id_departamento'); 
            $table->foreign('id_departamento')
                  ->references('id_departamento')
                  ->on('departamentos');
    
            // RELACIÓN CON CLASIFICACIONES
            $table->unsignedBigInteger('id_clasificacion');
            $table->foreign('id_clasificacion')
                  ->references('id_clasificacion')
                  ->on('clasificaciones');
    
            // RELACIÓN CON ESTATUS
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')
                  ->references('id_estatus')
                  ->on('estatus');
    
            $table->text('concepto')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisiciones');
    }
};
