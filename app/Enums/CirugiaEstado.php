<?php

namespace App\Enums;

enum CirugiaEstado: string
{
    case Pendiente = 'Pendiente';
    case EnCurso = 'En curso';
    case Cerrada = 'Cerrada';
    case Reprogramada = 'Reprogramada';
    case Cancelada = 'Cancelada';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
