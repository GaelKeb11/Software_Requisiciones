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
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id('id_cotizacion');
            $table->foreignId('id_requisicion')->constrained('requisiciones', 'id_requisicion');
            $table->text('nombre_proveedor'); // Text por encriptación
            $table->date('fecha_cotizacion')->nullable();
            $table->text('total_cotizado'); // Text por encriptación
            $table->foreignId('id_usuario_gestor')->constrained('users', 'id_usuario');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
