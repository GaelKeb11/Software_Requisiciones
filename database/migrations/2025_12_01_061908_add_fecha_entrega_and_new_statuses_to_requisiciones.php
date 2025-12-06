<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->date('fecha_entrega')->nullable()->after('hora_recepcion');
        });

        // Insert new statuses
        // We use predefined IDs to ensure logic in code matches.
        // Assuming 6 is 'Rechazadas', we start from 7.
        // Note: ID 2 was used as 'En Revisión' in old code.
        
        $statuses = [
            [
                'id_estatus' => 6,
                'nombre' => 'En Proceso de Compra',
                'color' => 'warning', // Amber/Orange
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_estatus' => 7,
                'nombre' => 'Lista para Entrega',
                'color' => 'info', // Blue
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_estatus' => 8,
                'nombre' => 'Completada',
                'color' => 'success', // Green
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($statuses as $status) {
            // Check if exists to avoid duplicate key errors if re-run manually or if IDs conflict
            if (!DB::table('estatus')->where('id_estatus', $status['id_estatus'])->exists()) {
                DB::table('estatus')->insert($status);
            } else {
                // Optional: Update name if it exists but is different? No, risky.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisiciones', function (Blueprint $table) {
            $table->dropColumn('fecha_entrega');
        });

        // We generally don't delete statuses in down() to preserve data integrity if they were used,
        // but for strict rollback:
        DB::table('estatus')->whereIn('id_estatus', [6, 7, 8])->delete();
    }
};
