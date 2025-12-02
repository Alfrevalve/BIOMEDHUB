<?php

namespace Database\Seeders;

use App\Models\Cirugia;
use App\Models\Equipo;
use App\Models\Institucion;
use App\Models\Movimiento;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Datos operativos mínimos para desarrollo
        $instituciones = Institucion::factory(3)->create();
        Equipo::factory(5)->create();
        Cirugia::factory(4)->create();
        Pedido::factory(4)->create();
        Movimiento::factory(3)->create([
            'institucion_id' => $instituciones->random()->id,
        ]);
    }
}
