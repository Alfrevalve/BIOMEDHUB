<?php

namespace App\Filament\Widgets;

use App\Models\Pedido;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PedidosUrgentes extends TableWidget
{
    protected static ?string $heading = 'Pedidos urgentes';
    protected static bool $isDiscovered = false;

    protected function getTableQuery(): Builder
    {
        return Pedido::query()
            ->with('cirugia')
            ->where('prioridad', 'Alta')
            ->whereNotIn('estado', ['Entregado', 'Anulado'])
            ->orderBy('fecha_entrega')
            ->orderBy('fecha');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('codigo_pedido')
                ->label('Pedido')
                ->limit(20)
                ->searchable(),
            TextColumn::make('cirugia.nombre')
                ->label('Cirugía')
                ->limit(40)
                ->toggleable(),
            TextColumn::make('fecha_entrega')
                ->label('Entrega')
                ->dateTime('d/m H:i')
                ->sortable(),
            TextColumn::make('estado')
                ->badge()
                ->sortable(),
            TextColumn::make('responsable')
                ->label('Responsable')
                ->limit(25)
                ->toggleable(),
        ];
    }
}
