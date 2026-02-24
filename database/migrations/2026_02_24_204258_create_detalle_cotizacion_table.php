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
        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->id('id_detalle_cotizacion');
            $table->foreignId('id_cotizacion')->constrained('cotizaciones', 'id_cotizacion')->cascadeOnDelete();
            $table->foreignId('id_detalle_requisicion')->nullable()->constrained('detalle_requisicions', 'id_detalle_requisicion');
            $table->decimal('cantidad_cotizada', 10, 2);
            $table->string('unidad_medida');
            $table->text('descripcion');
            $table->text('precio_unitario'); // Text por encriptación
            $table->text('subtotal'); // Text por encriptación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizacion');
    }
};
