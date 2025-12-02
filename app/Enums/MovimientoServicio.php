<?php

namespace App\Enums;

enum MovimientoServicio: string
{
    case Neuro = 'Neuro';
    case Columna = 'Columna';
    case Maxilofacial = 'Maxilofacial';
    case Otorrino = 'OTORRINO';
    case Otro = 'Otro';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
