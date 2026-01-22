<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_requisicions', function (Blueprint $table) {
            $table->unsignedBigInteger('id_clasificacion_detalle')->nullable()->after('id_requisicion');
            $table->foreign('id_clasificacion_detalle')
                ->references('id_clasificacion')
                ->on('clasificaciones')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detalle_requisicions', function (Blueprint $table) {
            $table->dropForeign(['id_clasificacion_detalle']);
            $table->dropColumn('id_clasificacion_detalle');
        });
    }
};
