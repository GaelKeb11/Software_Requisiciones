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
        Schema::create('detalle_requisicions', function (Blueprint $table) {
            $table->id('id_detalle_requisicion');
            $table->foreignId('id_requisicion')->constrained('requisiciones', 'id_requisicion')->cascadeOnDelete();
            $table->foreignId('id_clasificacion_detalle')->nullable()->constrained('clasificaciones', 'id_clasificacion');
            $table->integer('cantidad');
            $table->string('unidad_medida');
            $table->text('descripcion');
            $table->text('total'); // Text por encriptación
            $table->boolean('es_activo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_requisicions');
    }
};
