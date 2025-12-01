<?php

namespace App\Filament\Widgets;

use App\Models\Cirugia;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CirugiasProximas extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Cirugia::query()
            ->with('institucion')
            ->where('fecha_programada', '>=', now())
            ->orderBy('fecha_programada')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('fecha_programada')
                ->label('Fecha')
                ->dateTime('d/m H:i')
                ->sortable(),
            TextColumn::make('nombre')
                ->label('Cirugía')
                ->searchable()
                ->limit(40),
            TextColumn::make('institucion.nombre')
                ->label('Institución')
                ->limit(30)
                ->toggleable(),
            TextColumn::make('estado')
                ->badge()
                ->sortable(),
            TextColumn::make('tipo')
                ->badge(),
        ];
    }
}
