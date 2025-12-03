<?php

namespace App\Observers;

use App\Enums\EquipoEstado;
use App\Enums\MovimientoEstado;
use App\Models\Movimiento;

class MovimientoObserver
{
    public function created(Movimiento $movimiento): void
    {
        $this->sincronizarEquipo($movimiento);
    }

    public function updated(Movimiento $movimiento): void
    {
        $this->sincronizarEquipo($movimiento);
    }

    protected function sincronizarEquipo(Movimiento $movimiento): void
    {
        $equipo = $movimiento->equipo;
        if (! $equipo) {
            return;
        }

        // Mapear estado del movimiento a estado del equipo.
        $nuevoEstadoEquipo = match ($movimiento->estado_mov) {
            MovimientoEstado::Programado->value => EquipoEstado::Asignado->value,
            MovimientoEstado::EnUso->value => EquipoEstado::EnCirugia->value,
            MovimientoEstado::Devuelto->value => EquipoEstado::Disponible->value,
            default => $equipo->estado_actual,
        };

        $equipo->estado_actual = $nuevoEstadoEquipo;

        if (in_array($movimiento->estado_mov, [MovimientoEstado::Programado->value, MovimientoEstado::EnUso->value], true)) {
            // Mantener la institución de destino actualizada incluso si sólo cambia la institución.
            $equipo->institucion_id = $movimiento->institucion_id;
        }

        if ($movimiento->estado_mov === MovimientoEstado::Devuelto->value) {
            // Si hay centro configurado, úsalo; si no, se deja sin ubicación explícita.
            $equipo->institucion_id = config('biomedhub.centro_institucion_id') ?: null;

            if (! $movimiento->fecha_retorno) {
                $movimiento->updateQuietly([
                    'fecha_retorno' => now(),
                ]);
            }
        }

        $equipo->saveQuietly();
    }
}
