<?php

namespace App\Filament\Resources\Equipos\Tables;

use App\Enums\EquipoEstado;
use App\Enums\EquipoTipo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EquiposTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('codigo_interno')
                    ->searchable(),
                TextColumn::make('tipo')
                    ->badge(),
                TextColumn::make('estado_actual')
                    ->badge(),
                TextColumn::make('institucion.nombre')
                    ->label('Institución')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('marca_modelo')
                    ->searchable(),
                TextColumn::make('serie')
                    ->searchable(),
                TextColumn::make('responsable_actual')
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
                SelectFilter::make('tipo')->options(EquipoTipo::options()),
                SelectFilter::make('estado_actual')->options(EquipoEstado::options()),
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
