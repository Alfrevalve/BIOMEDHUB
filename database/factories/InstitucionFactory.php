<?php

namespace Database\Factories;

use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitucionFactory extends Factory
{
    protected $model = Institucion::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'tipo' => $this->faker->randomElement(['Publica', 'Privada', 'Militar', 'ONG']),
            'ciudad' => $this->faker->city(),
            'direccion' => $this->faker->streetAddress(),
            'contacto' => $this->faker->name(),
        ];
    }
}
