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
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento');
    
            // USAR ESTA SINTAXIS (La más segura):
            $table->foreignId('id_requisicion')
                  ->constrained('requisiciones', 'id_requisicion');
            $table->string('tipo_documento', 50);
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->text('comentarios')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
