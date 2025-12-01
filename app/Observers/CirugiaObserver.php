<?php

namespace App\Observers;

use App\Actions\CrearPedidoDesdeCirugia;
use App\Models\Cirugia;

class CirugiaObserver
{
    public function created(Cirugia $cirugia): void
    {
        app(CrearPedidoDesdeCirugia::class)($cirugia);
    }

    public function updated(Cirugia $cirugia): void
    {
        // Si activan el checkbox despues y aun no hay pedido: crear.
        if ($cirugia->wasChanged('crear_pedido_auto') && $cirugia->crear_pedido_auto) {
            app(CrearPedidoDesdeCirugia::class)($cirugia);
        }
    }
}
