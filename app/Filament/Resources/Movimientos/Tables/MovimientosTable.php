<?php

namespace App\Filament\Resources\Movimientos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MovimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('equipo_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('institucion_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cirugia_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->searchable(),
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
                //
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
