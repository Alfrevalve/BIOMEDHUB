<?php

namespace Database\Factories;

use App\Models\Equipo;
use App\Models\Institucion;
use App\Models\Movimiento;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovimientoFactory extends Factory
{
    protected $model = Movimiento::class;

    public function definition(): array
    {
        $fechaSalida = now()->addHours(rand(0, 6));

        return [
            'equipo_id' => Equipo::factory(),
            'institucion_id' => Institucion::factory(),
            'cirugia_id' => null,
            'nombre' => null,
            'fecha_salida' => $fechaSalida,
            'fecha_retorno' => null,
            'estado_mov' => 'Programado',
            'motivo' => 'Cirugia',
            'servicio' => $this->faker->randomElement(['Neuro', 'Columna', 'Maxilofacial', 'OTORRINO', 'Otro']),
            'material_enviado' => ['Set básico'],
            'entregado_por' => $this->faker->name(),
            'recibido_por' => null,
            'documento_soporte' => $this->faker->bothify('GUIA-###'),
        ];
    }
}
