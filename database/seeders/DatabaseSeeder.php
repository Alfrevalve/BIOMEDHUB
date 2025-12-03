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

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Datos operativos mínimos para desarrollo
        $instituciones = Institucion::factory(3)->create();
        $equipos = Equipo::factory(5)->create();
        $cirugias = Cirugia::factory(4)->create();

        // Pedidos coherentes con las cirugías creadas
        foreach ($cirugias as $cirugia) {
            Pedido::factory()->create([
                'cirugia_id' => $cirugia->id,
            ]);
        }

        // Movimientos con equipos e instituciones existentes
        foreach ($equipos->take(3) as $equipo) {
            Movimiento::factory()->create([
                'equipo_id' => $equipo->id,
                'institucion_id' => $instituciones->random()->id,
            ]);
        }

        $this->call(RolesSeeder::class);
    }
}
