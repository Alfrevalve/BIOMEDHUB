<?php

namespace App\Actions;

use App\Models\Cirugia;
use App\Models\Pedido;

class CrearPedidoDesdeCirugia
{
    public function __invoke(Cirugia $cirugia): ?Pedido
    {
        // Si no esta marcado, salimos.
        if (! $cirugia->crear_pedido_auto) {
            return null;
        }

        // Si ya tiene pedido, no duplicar.
        if ($cirugia->pedidos()->exists()) {
            return $cirugia->pedidos()->latest('id')->first();
        }

        // Fecha entrega = (fecha_programada - 1 dia) 16:00 Lima
        $fechaEntrega = optional($cirugia->fecha_programada)
            ? now('America/Lima')->setTimestamp(strtotime($cirugia->fecha_programada))
                ->subDay()->setTime(16, 0)
            : null;

        return Pedido::create([
            'cirugia_id'    => $cirugia->id,
            'codigo_pedido' => Pedido::generateCode(),
            'fecha'         => now('America/Lima')->toDateString(),
            'fecha_entrega' => $fechaEntrega,
            'estado'        => 'Solicitado',
            'prioridad'     => 'Alta',
            'entrega_a'     => $cirugia->institucion?->nombre,
            'responsable'   => auth()->user()?->name ?? 'Sistema',
        ]);
    }
}
