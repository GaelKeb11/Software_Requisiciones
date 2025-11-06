<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_requisicions', function (Blueprint $table) {
            $table->id('id_detalle_requisicion');
            
            // Llave foránea que conecta con la tabla de requisiciones
            $table->foreignId('id_requisicion')->constrained('requisiciones', 'id_requisicion')->cascadeOnDelete();
            
            $table->integer('cantidad');
            $table->string('unidad_medida'); // Ej: "Pieza", "Caja", "Servicio"
            $table->text('descripcion');
            $table->decimal('total', 10, 2)->nullable()->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_requisicions');
    }
};


