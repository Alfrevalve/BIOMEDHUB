<?php

namespace Database\Factories;

use App\Models\Equipo;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipoFactory extends Factory
{
    protected $model = Equipo::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Equipo ' . $this->faker->colorName(),
            'codigo_interno' => strtoupper($this->faker->bothify('EQ-###')),
            'tipo' => $this->faker->randomElement(['Craneo', 'Columna', 'Motor', 'Consola', 'Fresas']),
            'estado_actual' => 'Disponible',
            'institucion_id' => null,
            'marca_modelo' => $this->faker->word(),
            'serie' => strtoupper($this->faker->bothify('SER-#####')),
            'responsable_actual' => $this->faker->name(),
            'observaciones' => $this->faker->sentence(),
        ];
    }
}
