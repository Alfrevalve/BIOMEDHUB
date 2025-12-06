<?php

namespace App\Filament\Resources\Pedidos\Widgets;

use App\Models\Pedido;
use Filament\Widgets\Widget;

class PedidoReservasWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament.resources.pedidos.components.reservas';

    public ?Pedido $record = null;

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $reservas = collect();

        if ($this->record && method_exists($this->record, 'reservas')) {
            $reservas = $this->record->reservas()->with('item')->get();
        }

        return [
            'reservas' => $reservas,
        ];
    }
}
