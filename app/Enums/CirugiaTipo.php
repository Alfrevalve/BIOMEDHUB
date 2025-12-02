<?php

namespace App\Enums;

enum CirugiaTipo: string
{
    case Craneo = 'Craneo';
    case Columna = 'Columna';
    case Tumor = 'Tumor';
    case Pediatrica = 'Pediatrica';
    case Otro = 'Otro';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
