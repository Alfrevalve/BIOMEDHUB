<?php

namespace Database\Seeders;

use App\Models\Cirugia;
use App\Models\Item;
use App\Models\ItemKit;
use App\Models\ItemKitItem;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Items de inventario
        $fresa = Item::firstOrCreate(
            ['sku' => 'FRESA-001'],
            ['nombre' => 'Fresa estándar', 'tipo' => 'Fresa', 'stock_total' => 50]
        );
        $adaptador = Item::firstOrCreate(
            ['sku' => 'ADAP-001'],
            ['nombre' => 'Adaptador universal', 'tipo' => 'Adaptador', 'stock_total' => 30]
        );
        $tubo = Item::firstOrCreate(
            ['sku' => 'TUBO-001'],
            ['nombre' => 'Tubo de succión', 'tipo' => 'Tubo', 'stock_total' => 40]
        );

        // Kit demo
        $kitBasico = ItemKit::firstOrCreate(
            ['codigo' => 'KIT-BASICO'],
            ['nombre' => 'Kit básico neuro', 'descripcion' => 'Fresa + Adaptador + Tubo']
        );

        ItemKitItem::firstOrCreate(
            ['item_kit_id' => $kitBasico->id, 'item_id' => $fresa->id],
            ['cantidad' => 2]
        );
        ItemKitItem::firstOrCreate(
            ['item_kit_id' => $kitBasico->id, 'item_id' => $adaptador->id],
            ['cantidad' => 1]
        );
        ItemKitItem::firstOrCreate(
            ['item_kit_id' => $kitBasico->id, 'item_id' => $tubo->id],
            ['cantidad' => 2]
        );

        // Cirugía y pedido de muestra con kit reservado
        $cirugia = Cirugia::firstOrCreate(
            ['nombre' => 'Craneotomía demo', 'fecha_programada' => now()->addDays(2)],
            [
                'institucion_id' => \App\Models\Institucion::first()->id ?? null,
                'estado' => 'Pendiente',
                'tipo' => 'Craneo',
                'crear_pedido_auto' => false,
            ]
        );

        Pedido::firstOrCreate(
            ['cirugia_id' => $cirugia->id, 'item_kit_id' => $kitBasico->id],
            [
                'codigo_pedido' => Pedido::generateCode(),
                'fecha' => now()->toDateString(),
                'fecha_entrega' => now()->addDay(),
                'estado' => 'Solicitado',
                'prioridad' => 'Alta',
                'entrega_a' => $cirugia->institucion?->nombre,
                'responsable' => 'Sistema',
            ]
        );

        // Usuarios de prueba por rol (contraseñas en .env si se definen)
        $roles = [
            'logistica' => 'logistica@example.com',
            'instrumentista' => 'instrumentista@example.com',
            'comercial' => 'comercial@example.com',
            'soporte_biomedico' => 'soporte@example.com',
            'auditoria' => 'auditoria@example.com',
        ];

        foreach ($roles as $rol => $email) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($rol),
                    'password' => env('DEMO_PASSWORD', 'Password123!'),
                ]
            );
            $user->syncRoles([$rol]);
        }
    }
}
