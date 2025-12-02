<?php

namespace Database\Factories;

use App\Models\Pedido;
use App\Models\Cirugia;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        $fechaEntrega = $this->faker->dateTimeBetween('+0 days', '+3 days');

        return [
            'cirugia_id' => Cirugia::factory(),
            'codigo_pedido' => Pedido::generateCode(),
            'fecha' => now()->toDateString(),
            'fecha_entrega' => $fechaEntrega,
            'estado' => $this->faker->randomElement(['Solicitado', 'Preparacion', 'Despachado']),
            'prioridad' => $this->faker->randomElement(['Alta', 'Media', 'Baja']),
            'entrega_a' => $this->faker->company(),
            'responsable' => $this->faker->name(),
        ];
    }
}
