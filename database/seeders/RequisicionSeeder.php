<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Recepcion\Departamento;
use App\Models\Recepcion\Clasificacion;
use App\Models\Recepcion\Requisicion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


use function Symfony\Component\Clock\now;

class RequisicionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $requisicion= [
          [
            'folio' => 'DA-2026-0001',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requsicion 1',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 8
          ],

          [
            'folio' => 'DA-2026-0002',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 2',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 8
          ],

          
          [
            'folio' => 'DA-2026-0003',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 3',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 2
          ],
          
          [
            'folio' => 'DA-2026-0004',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 4',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 2
          ],
          
          [
            'folio' => 'DA-2026-0005',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 5',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 4
          ],
          
          [
            'folio' => 'DA-2026-0006',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 6',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 3
          ],
          
          [
            'folio' => 'DA-2026-0007',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 7',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 6
          ],
          
          [
            'folio' => 'DA-2026-0008',
            'fecha_creacion' => Carbon::now()->toDateString(), 
            'fecha_recepcion' => Carbon::now()->toDateTimeString(),
            'hora_recepcion' => Carbon::now()->toDateTimeString(),
            'concepto' => 'Prueba de requisicion 8',
            'id_departamento' => 27,
            'id_clasificacion' => Clasificacion::inRandomOrder()->first()->id_clasificacion ,
            'id_usuario' => 1,
            'id_solicitante' => 2,
            'id_estatus' => 8
          ],
        ];

        DB::table('requisiciones')->insert($requisicion);

        
    }
}
