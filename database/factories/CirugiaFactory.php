<?php

namespace Database\Factories;

use App\Models\Cirugia;
use App\Models\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CirugiaFactory extends Factory
{
    protected $model = Cirugia::class;

    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'nombre' => 'Cirugía ' . $this->faker->words(2, true),
            'fecha_programada' => now()->addDays(rand(0, 5))->setTime(rand(7, 18), [0, 15, 30, 45][rand(0, 3)]),
            'estado' => $this->faker->randomElement(['Pendiente', 'En curso', 'Cerrada', 'Reprogramada']),
            'cirujano_principal' => $this->faker->name(),
            'instrumentista_asignado' => $this->faker->name(),
            'tipo' => $this->faker->randomElement(['Craneo', 'Columna', 'Tumor', 'Pediatrica', 'Otro']),
            'crear_pedido_auto' => true,
            'paciente_codigo' => strtoupper($this->faker->bothify('PAC-###')),
            'monto_soles' => $this->faker->randomFloat(2, 1000, 5000),
        ];
    }
}
