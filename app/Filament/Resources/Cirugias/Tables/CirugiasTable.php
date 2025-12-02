<?php

namespace App\Filament\Resources\Cirugias\Tables;

use App\Enums\CirugiaEstado;
use App\Enums\CirugiaTipo;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CirugiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('institucion.nombre')
                    ->label('Institución')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre')
                    ->label('Cirugía')
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
                SelectFilter::make('estado')
                    ->options(CirugiaEstado::options()),
                SelectFilter::make('tipo')
                    ->options(CirugiaTipo::options()),
                Filter::make('rango_fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $date) => $q->whereDate('fecha_programada', '>=', $date))
                            ->when($data['hasta'] ?? null, fn ($q, $date) => $q->whereDate('fecha_programada', '<=', $date));
                    }),
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
