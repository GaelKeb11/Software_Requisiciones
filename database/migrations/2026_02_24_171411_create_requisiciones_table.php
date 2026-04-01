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
            $table->date('fecha_creacion');
            $table->timestamp('fecha_recepcion')->useCurrent();;
            $table->timestamp('hora_recepcion')->useCurrent();;
            $table->date('fecha_entrega')->nullable();
            $table->text('concepto')->nullable();

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
    
            // RELACIÓN CON USUARIO
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('users');
            // RELACIÓN CON ESTATUS
            $table->unsignedBigInteger('id_estatus');
            $table->foreign('id_estatus')
                  ->references('id_estatus')
                  ->on('estatus');
            // SOLICITANTE
            $table->unsignedBigInteger('id_solicitante');
            $table->foreign('id_solicitante')
                  ->references('id_usuario')
                  ->on('users');
    
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
