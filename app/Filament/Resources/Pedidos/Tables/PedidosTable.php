<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugía')
                    ->searchable(),
                TextColumn::make('codigo_pedido')
                    ->searchable(),
                TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('fecha_entrega')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge(),
                TextColumn::make('prioridad')
                    ->badge(),
                TextColumn::make('entrega_a')
                    ->searchable(),
                TextColumn::make('responsable')
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
                SelectFilter::make('estado')->options(PedidoEstado::options()),
                SelectFilter::make('prioridad')->options(PedidoPrioridad::options()),
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
