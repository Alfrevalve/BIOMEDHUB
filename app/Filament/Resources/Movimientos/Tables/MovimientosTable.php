<?php

namespace App\Filament\Resources\Movimientos\Tables;

use App\Enums\MovimientoEstado;
use App\Enums\MovimientoMotivo;
use App\Enums\MovimientoServicio;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MovimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipo.nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institucion.nombre')
                    ->label('Institución')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugía')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre')
                    ->label('Movimiento')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('fecha_salida')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fecha_retorno')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estado_mov')
                    ->badge(),
                TextColumn::make('motivo')
                    ->badge(),
                TextColumn::make('servicio')
                    ->badge(),
                TextColumn::make('entregado_por')
                    ->searchable(),
                TextColumn::make('recibido_por')
                    ->searchable(),
                TextColumn::make('documento_soporte')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado_mov')->options(MovimientoEstado::options()),
                SelectFilter::make('motivo')->options(MovimientoMotivo::options()),
                SelectFilter::make('servicio')->options(MovimientoServicio::options()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
