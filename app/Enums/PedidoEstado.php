<?php

namespace App\Enums;

enum PedidoEstado: string
{
    case Solicitado = 'Solicitado';
    case Preparacion = 'Preparacion';
    case Despachado = 'Despachado';
    case Entregado = 'Entregado';
    case Devuelto = 'Devuelto';
    case Anulado = 'Anulado';
    case Observado = 'Observado';

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->value])
            ->all();
    }
}
