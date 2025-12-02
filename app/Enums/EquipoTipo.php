<?php

namespace App\Enums;

enum EquipoTipo: string
{
    case Craneo = 'Craneo';
    case Columna = 'Columna';
    case Motor = 'Motor';
    case Consola = 'Consola';
    case Fresas = 'Fresas';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
