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
        if (!Schema::hasTable('ordenes_compra')) {
            Schema::create('ordenes_compra', function (Blueprint $table) {
                $table->id('id_orden_compra');
                $table->foreignId('id_requisicion')->constrained('requisiciones', 'id_requisicion');
                $table->string('nombre_proveedor');
                $table->date('fecha_orden');
                $table->decimal('total_calculado', 10, 2);
                $table->foreignId('id_usuario_gestor')->constrained('users', 'id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};
