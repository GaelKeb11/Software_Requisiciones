<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleRequisicionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detalles=[
            [
                'id_requisicion'=>1,
                'id_clasificacion_detalle'=> 8,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>2,
                'id_clasificacion_detalle'=> 12,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>3,
                'id_clasificacion_detalle'=> 14,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>4,
                'id_clasificacion_detalle'=> 35,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>5,
                'id_clasificacion_detalle'=> 49,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>2,
                'id_clasificacion_detalle'=> 41,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>2,
                'id_clasificacion_detalle'=> 113,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],
            [
                'id_requisicion'=>8,
                'id_clasificacion_detalle'=> 149,
                'cantidad'=>1,
                'unidad_medida'=>'Equipos',
                'descripcion'=>'Equipos nuevos',
                'total'=>0,
                'es_activo'=>0,
            ],

        ];

        DB::table('detalle_requisicions')->insert($detalles);
    }
}
