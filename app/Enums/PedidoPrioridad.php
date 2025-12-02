<?php

namespace App\Enums;

enum PedidoPrioridad: string
{
    case Alta = 'Alta';
    case Media = 'Media';
    case Baja = 'Baja';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
