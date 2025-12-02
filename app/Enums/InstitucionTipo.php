<?php

namespace App\Enums;

enum InstitucionTipo: string
{
    case Publica = 'Publica';
    case Privada = 'Privada';
    case Militar = 'Militar';
    case ONG = 'ONG';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
