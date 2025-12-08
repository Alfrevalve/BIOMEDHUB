<?php

return [
    // ID de institucion (almacen central) a la que se asignan los equipos al marcar un movimiento como Devuelto.
    // Definir en .env: CENTRO_INSTITUCION_ID=1
    'centro_institucion_id' => env('CENTRO_INSTITUCION_ID'),

    // Umbral para stock critico (items con disponible <= umbral).
    'stock_critico_threshold' => env('STOCK_CRITICO_THRESHOLD', 5),
];
