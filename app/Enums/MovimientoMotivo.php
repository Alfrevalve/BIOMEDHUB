<?php

namespace App\Enums;

enum MovimientoMotivo: string
{
    case Cirugia = 'Cirugia';
    case Prestamo = 'Prestamo';
    case Consignacion = 'Consignacion';
    case Mantenimiento = 'Mantenimiento';
    case Demostracion = 'Demostracion';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
