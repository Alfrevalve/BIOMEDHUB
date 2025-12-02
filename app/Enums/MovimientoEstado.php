<?php

namespace App\Enums;

enum MovimientoEstado: string
{
    case Programado = 'Programado';
    case EnUso = 'En uso';
    case Devuelto = 'Devuelto';
    case Observado = 'Observado';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
