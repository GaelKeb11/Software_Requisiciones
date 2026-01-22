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
            
            $table->unsignedBigInteger('id_requisicion');
            $table->foreign('id_requisicion')->references('id_requisicion')->on('requisiciones');
            
            $table->string('nombre_proveedor')->nullable();
            $table->date('fecha_cotizacion')->nullable();
            $table->decimal('total_cotizado', 10, 2)->default(0);
            
            $table->unsignedBigInteger('id_usuario_gestor');
            $table->foreign('id_usuario_gestor')
                ->references('id')
                ->on('users');

            $table->timestamps();
        });

        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->id('id_detalle_cotizacion');
            
            $table->unsignedBigInteger('id_cotizacion');
            $table->foreign('id_cotizacion')->references('id_cotizacion')->on('cotizaciones')->onDelete('cascade');

            $table->unsignedBigInteger('id_detalle_requisicion')->nullable();
            $table->foreign('id_detalle_requisicion')->references('id_detalle_requisicion')->on('detalle_requisicions');
            
            $table->decimal('cantidad_cotizada', 10, 2);
            $table->string('unidad_medida');
            $table->text('descripcion');
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
        Schema::dropIfExists('detalle_cotizacion');
        Schema::dropIfExists('cotizaciones');
    }
};
