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
        // Table: cotizaciones
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->text('nombre_proveedor')->change();
            $table->text('total_cotizado')->change();
        });

        // Table: detalle_cotizacion
        Schema::table('detalle_cotizacion', function (Blueprint $table) {
            $table->text('descripcion')->change();
            $table->text('precio_unitario')->change();
            $table->text('subtotal')->change();
        });

        // Table: ordenes_compra
        Schema::table('ordenes_compra', function (Blueprint $table) {
            $table->text('nombre_proveedor')->change();
            $table->text('total_calculado')->change();
        });

        // Table: detalle_orden_compra
        Schema::table('detalle_orden_compra', function (Blueprint $table) {
            $table->text('precio_unitario')->change();
            $table->text('subtotal')->change();
        });

        // Table: requisiciones
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->text('concepto')->nullable()->change();
        });

        // Table: detalle_requisicions
        Schema::table('detalle_requisicions', function (Blueprint $table) {
            $table->text('descripcion')->change();
            $table->text('total')->change();
        });

        // Table: users
        Schema::table('users', function (Blueprint $table) {
            $table->text('numero_telefonico')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     * NOTE: This is a destructive action and data types will not be the same as before.
     */
    public function down(): void
    {
        // Reversing is complex as we don't know the original types.
        // This is a placeholder. Manually check original migration files for exact types.
        Schema::table('cotizaciones', function (Blueprint $table) {
            $table->string('nombre_proveedor')->change();
            $table->decimal('total_cotizado', 8, 2)->change();
        });
        
        // ... add reversals for other tables if needed, checking original types
    }
};
