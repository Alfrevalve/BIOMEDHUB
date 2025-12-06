<?php

namespace App\Filament\Resources\Movimientos\Tables;

use App\Enums\MovimientoEstado;
use App\Enums\MovimientoMotivo;
use App\Enums\MovimientoServicio;
use App\Models\User;
use App\Notifications\RecogidaMaterialNotification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification;

class MovimientosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_salida', 'asc')
            ->striped()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Movimiento')
                    ->searchable()
                    ->weight('semibold')
                    ->limit(50),
                TextColumn::make('estado_mov')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Programado' => 'info',
                        'En uso' => 'primary',
                        'Devuelto' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('fecha_salida')
                    ->label('Salida')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('fecha_retorno')
                    ->label('Retorno')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('motivo')
                    ->label('Motivo')
                    ->badge(),
                TextColumn::make('servicio')
                    ->label('Servicio')
                    ->badge(),
                TextColumn::make('equipo.nombre')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('institucion.nombre')
                    ->label('Institucion')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('pedido.codigo_pedido')
                    ->label('Pedido')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('entregado_por')
                    ->label('Entregado por')
                    ->searchable(),
                TextColumn::make('recibido_por')
                    ->label('Recibido por')
                    ->searchable(),
                TextColumn::make('transportista')
                    ->label('Transportista')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('transportista_contacto')
                    ->label('Contacto transportista')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('documento_soporte')
                    ->label('Documento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('recogida_solicitada_at')
                    ->label('Recojo solicitado')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('antiguedad_recojo')
                    ->label('Antiguedad')
                    ->badge()
                    ->color(function ($record) {
                        $date = $record->recogida_solicitada_at;
                        if (! $date) {
                            return 'gray';
                        }
                        $hours = now()->diffInHours($date);
                        return $hours > 48 ? 'danger' : ($hours > 24 ? 'warning' : 'success');
                    })
                    ->formatStateUsing(function ($record) {
                        return $record->recogida_solicitada_at
                            ? $record->recogida_solicitada_at->diffForHumans()
                            : 'Sin fecha';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('estado_mov')->options(MovimientoEstado::options()),
                SelectFilter::make('motivo')->options(MovimientoMotivo::options()),
                SelectFilter::make('servicio')->options(MovimientoServicio::options()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->tooltip('Editar movimiento'),
                    Action::make('marcar_en_uso')
                        ->label('Marcar en uso')
                        ->icon('heroicon-o-play')
                        ->color('primary')
                        ->visible(fn ($record) => $record->estado_mov === 'Programado')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['estado_mov' => 'En uso'])),
                    Action::make('solicitar_recogida')
                        ->label('Solicitar recogida')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->form([
                            TagsInput::make('material_usado')
                                ->label('Material usado')
                                ->placeholder('Anadir item')
                                ->separator(',')
                                ->columnSpanFull(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'material_usado' => $data['material_usado'] ?? [],
                                'estado_mov' => 'Devuelto',
                                'fecha_retorno' => now(),
                                'recogida_solicitada_at' => now(),
                            ]);

                            $recipients = User::role(['logistica', 'soporte_biomedico'])->get();
                            if ($recipients->isNotEmpty()) {
                                Notification::send($recipients, new RecogidaMaterialNotification($record));
                            }
                        }),
                    Action::make('marcar_recogido')
                        ->label('Marcar recogido')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->visible(fn ($record) => $record->recogida_solicitada_at !== null)
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'estado_mov' => 'Devuelto',
                                'fecha_retorno' => $record->fecha_retorno ?: now(),
                            ]);

                            if ($record->pedido && $record->pedido->estado !== 'Devuelto') {
                                $record->pedido->update(['estado' => 'Devuelto']);
                            }
                        }),
                    Action::make('confirmar_devolucion')
                        ->label('Confirmar devolucion')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn ($record) => in_array($record->estado_mov, ['En uso', 'Programado']))
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $record->update([
                                'estado_mov' => 'Devuelto',
                                'fecha_retorno' => now(),
                            ]);
                        }),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
