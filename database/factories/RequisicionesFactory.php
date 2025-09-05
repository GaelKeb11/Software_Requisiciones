<?php

namespace Database\Factories;

use App\Models\Recepcion\Requisicion;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequisicionFactory extends Factory
{
    protected $model = Requisicion::class;

    public function definition()
    {
        return [
            'folio' => $this->faker->unique()->bothify('REQ-#####'),
            'fecha_creacion' => $this->faker->date(),
            'fecha_recepcion' => $this->faker->date(),
            'hora_recepcion' => $this->faker->time(),
            'concepto' => $this->faker->sentence(),
            'id_departamento' => \App\Models\Recepcion\Departamento::factory(),
            'id_clasificacion' => \App\Models\Recepcion\Clasificacion::factory(),
            'id_usuario' => \App\Models\User::factory(),
            'id_estatus' => \App\Models\Recepcion\Estatus::factory()
        ];
    }
}