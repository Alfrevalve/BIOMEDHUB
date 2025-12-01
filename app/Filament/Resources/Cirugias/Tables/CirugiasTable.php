<?php

namespace App\Filament\Resources\Cirugias\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CirugiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('institucion_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('fecha_programada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('estado')
                    ->badge(),
                TextColumn::make('cirujano_principal')
                    ->searchable(),
                TextColumn::make('instrumentista_asignado')
                    ->searchable(),
                TextColumn::make('tipo')
                    ->badge(),
                IconColumn::make('crear_pedido_auto')
                    ->boolean(),
                TextColumn::make('paciente_codigo')
                    ->searchable(),
                TextColumn::make('monto_soles')
                    ->numeric()
                    ->sortable(),
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
