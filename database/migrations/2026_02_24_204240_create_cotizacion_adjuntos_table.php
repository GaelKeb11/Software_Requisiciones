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
        Schema::create('cotizacion_adjuntos', function (Blueprint $table) {
            $table->id('id_adjunto');
            $table->foreignId('id_cotizacion')->constrained('cotizaciones', 'id_cotizacion')->cascadeOnDelete();
            $table->string('nombre_archivo')->nullable();
            $table->string('ruta_archivo');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_adjuntos');
    }
};
