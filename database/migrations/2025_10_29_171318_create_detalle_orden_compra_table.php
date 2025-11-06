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
        Schema::create('detalle_orden_compra', function (Blueprint $table) {
            $table->id('id_detalle_orden_compra');
            $table->foreignId('id_orden_compra')->constrained('ordenes_compra', 'id_orden_compra')->cascadeOnDelete();
            $table->foreignId('id_detalle_requisicion')->constrained('detalle_requisicions', 'id_detalle_requisicion');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden_compra');
    }
};
