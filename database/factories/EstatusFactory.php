<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recepcion\Estatus>
 */
class EstatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'nombre' => $this->faker->randomElement(['Recepcionada', 'Asignada', 'Cotización', 'Aprobada', 'Rechazada']),
            'color' => $this->faker->safeColorName,
        ];
    }
}
