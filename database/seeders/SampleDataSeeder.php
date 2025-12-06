<?php

namespace Database\Seeders;

use App\Models\Cirugia;
use App\Models\Item;
use App\Models\ItemKit;
use App\Models\ItemKitItem;
use App\Models\Pedido;
use App\Models\Movimiento;
use App\Models\Equipo;
use App\Models\CirugiaReporte;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Items de inventario
        $fresa = Item::firstOrCreate(
            ['sku' => 'FRESA-001'],
            ['nombre' => 'Fresa estandar', 'tipo' => 'Fresa', 'stock_total' => 50]
        );
        $adaptador = Item::firstOrCreate(
            ['sku' => 'ADAP-001'],
            ['nombre' => 'Adaptador universal', 'tipo' => 'Adaptador', 'stock_total' => 30]
        );
        $tubo = Item::firstOrCreate(
            ['sku' => 'TUBO-001'],
            ['nombre' => 'Tubo de succion', 'tipo' => 'Tubo', 'stock_total' => 40]
        );

        // Kit demo
        $kitBasico = ItemKit::firstOrCreate(
            ['codigo' => 'KIT-BASICO'],
            ['nombre' => 'Kit basico neuro', 'descripcion' => 'Fresa + Adaptador + Tubo']
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

        // Cirugia y pedido de muestra con kit reservado
        $cirugia = Cirugia::firstOrCreate(
            ['nombre' => 'Craneotomia demo', 'fecha_programada' => now()->addDays(2)],
            [
                'institucion_id' => \App\Models\Institucion::first()->id ?? null,
                'estado' => 'Pendiente',
                'tipo' => 'Craneo',
                'crear_pedido_auto' => false,
            ]
        );

        $instrumentista = User::where('email', 'instrumentista@example.com')->first();
        if ($instrumentista && ! $cirugia->instrumentista_id) {
            $cirugia->instrumentista_id = $instrumentista->id;
            $cirugia->instrumentista_asignado = $instrumentista->name;
            $cirugia->save();
        }

        $pedido = Pedido::firstOrCreate(
            ['cirugia_id' => $cirugia->id, 'item_kit_id' => $kitBasico->id],
            [
                'codigo_pedido' => Pedido::generateCode(),
                'fecha' => now()->toDateString(),
                'fecha_entrega' => now()->addDay(),
                'estado' => 'Solicitado',
                'prioridad' => 'Alta',
                'entrega_a' => $cirugia->institucion?->nombre,
                'responsable' => 'Sistema',
                'material_detalle' => [
                    ['descripcion' => 'Fresa estandar', 'cantidad' => 2],
                    ['descripcion' => 'Adaptador universal', 'cantidad' => 1],
                ],
                'equipo_detalle' => [
                    ['equipo' => 'Equipo demo neuro', 'codigo' => 'EQ-001'],
                ],
            ]
        );

        // Equipo y movimiento con recojo pendiente
        $equipo = Equipo::firstOrCreate(
            ['nombre' => 'Equipo demo neuro'],
            [
                'estado_actual' => 'Asignado',
                'codigo_interno' => 'EQ-001',
                'tipo' => 'Craneo',
                'institucion_id' => $cirugia->institucion_id,
            ]
        );

        Movimiento::firstOrCreate(
            ['equipo_id' => $equipo->id, 'cirugia_id' => $cirugia->id],
            [
                'institucion_id' => $cirugia->institucion_id,
                'pedido_id' => $pedido->id,
                'nombre' => 'Equipo demo -> traslado',
                'fecha_salida' => now()->subDay(),
                'fecha_retorno' => null,
                'estado_mov' => 'Programado',
                'motivo' => 'Cirugia',
                'servicio' => 'Neuro',
                'material_enviado' => ['Fresa', 'Adaptador'],
                'recogida_solicitada_at' => now()->subHours(6),
            ]
        );

        // Reporte de consumo de ejemplo
        CirugiaReporte::firstOrCreate(
            ['cirugia_id' => $cirugia->id],
            [
                'institucion' => $cirugia->institucion?->nombre,
                'paciente' => $cirugia->paciente_codigo ?? 'Paciente demo',
                'hora_programada' => $cirugia->fecha_programada,
                'hora_inicio' => now()->addDays(2)->setTime(9, 0),
                'hora_termino' => now()->addDays(2)->setTime(11, 0),
                'consumo' => 'Fresa x2, Adaptador x1',
                'notas' => 'Caso demo para QA',
                'evidencia_path' => null,
            ]
        );

        // Usuarios de prueba por rol (contrasenas en .env si se definen)
        $roles = [
            'logistica' => 'logistica@example.com',
            'instrumentista' => 'instrumentista@example.com',
            'comercial' => 'comercial@example.com',
            'soporte_biomedico' => 'soporte@example.com',
            'auditoria' => 'auditoria@example.com',
            'almacen' => 'almacen@example.com',
            'facturacion' => 'facturacion@example.com',
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
