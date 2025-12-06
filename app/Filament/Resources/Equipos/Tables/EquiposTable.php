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
            ->defaultSort('nombre')
            ->striped()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('codigo_interno')
                    ->label('Codigo interno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('estado_actual')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Disponible' => 'success',
                        'En uso' => 'primary',
                        'En mantenimiento' => 'warning',
                        'Baja' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('institucion.nombre')
                    ->label('Institucion')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('marca_modelo')
                    ->label('Marca/Modelo')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('serie')
                    ->label('Serie')
                    ->searchable(),
                TextColumn::make('responsable_actual')
                    ->label('Responsable')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo')->options(EquipoTipo::options()),
                SelectFilter::make('estado_actual')->options(EquipoEstado::options()),
            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip('Editar equipo'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
