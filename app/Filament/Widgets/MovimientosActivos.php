<?php

namespace App\Filament\Widgets;

use App\Models\Movimiento;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class MovimientosActivos extends TableWidget
{
    protected function getTableQuery(): Builder
    {
        return Movimiento::query()
            ->with(['equipo', 'institucion'])
            ->whereIn('estado_mov', ['Programado', 'En uso'])
            ->orderByDesc('fecha_salida');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('equipo.nombre')
                ->label('Equipo')
                ->limit(25)
                ->searchable(),
            TextColumn::make('institucion.nombre')
                ->label('Institución')
                ->limit(30)
                ->toggleable(),
            TextColumn::make('estado_mov')
                ->badge()
                ->sortable(),
            TextColumn::make('fecha_salida')
                ->label('Salida')
                ->dateTime('d/m H:i')
                ->sortable(),
            TextColumn::make('fecha_retorno')
                ->label('Retorno')
                ->dateTime('d/m H:i')
                ->sortable(),
        ];
    }
}
