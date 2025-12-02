<?php

namespace App\Enums;

enum EquipoEstado: string
{
    case Disponible = 'Disponible';
    case EnCirugia = 'En cirugia';
    case Asignado = 'Asignado';
    case EnMantenimiento = 'En mantenimiento';
    case EnTransito = 'En transito';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
