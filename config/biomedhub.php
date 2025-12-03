<?php

return [
    // ID de institución (almacén central) a la que se asignan los equipos al marcar un movimiento como Devuelto.
    // Definir en .env: CENTRO_INSTITUCION_ID=1
    'centro_institucion_id' => env('CENTRO_INSTITUCION_ID'),
];
