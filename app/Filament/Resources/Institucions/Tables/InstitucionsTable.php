<?php

namespace App\Filament\Resources\Institucions\Tables;

use App\Enums\InstitucionTipo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InstitucionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->striped()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Institucion')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Publica' => '#0ea5e9',   // azul
                        'Privada' => '#f97316',   // naranja
                        'Militar' => '#f59e0b',   // ámbar
                        'ONG' => '#10b981',       // verde
                        default => '#6b7280',
                    }),
                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->searchable(),
                TextColumn::make('direccion')
                    ->label('Direccion')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('contacto')
                    ->label('Contacto')
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
                SelectFilter::make('tipo')->options(InstitucionTipo::options()),
            ])
            ->recordActions([
                EditAction::make()
                    ->tooltip('Editar institucion'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
