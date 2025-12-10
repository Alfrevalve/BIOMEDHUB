<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class StockBajo extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static bool $isDiscovered = false;

    protected function getTableQuery(): Builder
    {
        $availableExpr = 'CAST(COALESCE(stock_total,0) AS SIGNED) - CAST(COALESCE(stock_reservado,0) AS SIGNED)';
        $critico = (int) config('biomedhub.stock_critico_threshold', 5);

        return Item::query()
            ->selectRaw("id, sku, nombre, stock_total, stock_reservado, {$availableExpr} as disponible_calc")
            ->whereRaw("{$availableExpr} <= ?", [$critico])
            ->orderByRaw("{$availableExpr} asc");
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('sku')
                ->label('SKU')
                ->limit(18)
                ->tooltip(fn ($state) => $state),
            TextColumn::make('nombre')
                ->label('Item')
                ->limit(40)
                ->tooltip(fn ($state) => $state),
            TextColumn::make('stock_total')
                ->label('Stock total')
                ->sortable(),
            TextColumn::make('stock_reservado')
                ->label('Reservado')
                ->sortable(),
            TextColumn::make('disponible')
                ->label('Disponible')
                ->state(fn (Item $record) => $record->disponible())
                ->badge()
                ->color(fn (int $state) => $state <= 2 ? 'danger' : 'warning'),
        ];
    }
}
