<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use App\Models\User;
use App\Models\Pedido;
use App\Notifications\PedidoListoDespachoNotification;
use App\Notifications\PedidoTransitionNotification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification;

class PedidosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_entrega', 'asc')
            ->striped()
            ->columns([
                TextColumn::make('codigo_pedido')
                    ->label('Pedido')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('fecha_entrega')
                    ->label('Fecha entrega')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('entregado_en_institucion_at')
                    ->label('Entregado en institucion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Solicitado' => 'info',
                        'Preparacion' => 'warning',
                        'Despachado' => 'primary',
                        'Entregado' => 'success',
                        'Devuelto' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Alta' => 'danger',
                        'Media' => 'warning',
                        'Baja' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('entrega_a')
                    ->label('Entrega a')
                    ->searchable(),
                TextColumn::make('responsable')
                    ->label('Responsable')
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
                TextColumn::make('itemKit.nombre')
                    ->label('Kit')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('material_detalle')
                    ->label('Materiales')
                    ->formatStateUsing(function ($state) {
                        if (! is_array($state) || empty($state)) {
                            return 'Sin detalle';
                        }
                        return collect($state)->map(function ($row) {
                            $desc = $row['descripcion'] ?? 'Material';
                            $cant = $row['cantidad'] ?? 1;
                            return "{$desc} (x{$cant})";
                        })->implode(', ');
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('equipo_detalle')
                    ->label('Equipos')
                    ->formatStateUsing(function ($state) {
                        if (! is_array($state) || empty($state)) {
                            return 'Sin equipos';
                        }
                        return collect($state)->map(function ($row) {
                            $eq = $row['equipo'] ?? 'Equipo';
                            $cod = $row['codigo'] ?? null;
                            return $cod ? "{$eq} ({$cod})" : $eq;
                        })->implode(', ');
                    })
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('evidencia_consumo')
                    ->label('Evidencia consumo')
                    ->state(function ($record) {
                        return $record->cirugia?->reportes()->whereNotNull('evidencia_path')->exists();
                    })
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-exclamation-circle')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->tooltip(fn ($state) => $state ? 'Con evidencia' : 'Falta evidencia'),
                TextColumn::make('fecha')
                    ->label('Creado')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')->options(PedidoEstado::options()),
                SelectFilter::make('prioridad')->options(PedidoPrioridad::options()),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->tooltip('Editar pedido'),
                    Action::make('a_preparacion')
                        ->label('Mover a preparacion')
                        ->icon('heroicon-o-wrench-screwdriver')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->estado === 'Solicitado')
                        ->action(function ($record) {
                            $record->update(['estado' => 'Preparacion']);
                            self::notifyRoles($record, 'Preparacion');
                        }),
                    Action::make('marcar_listo_despacho')
                        ->label('Listo para despacho')
                        ->icon('heroicon-o-bell-alert')
                        ->color('info')
                        ->visible(fn ($record) => in_array($record->estado, ['Solicitado', 'Preparacion']))
                        ->form([
                            TextInput::make('transportista')
                                ->label('Transportista')
                                ->placeholder('Nombre de la empresa o persona')
                                ->required(),
                            TextInput::make('transportista_contacto')
                                ->label('Contacto / telefono')
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update([
                                'estado' => 'Preparacion',
                                'listo_despacho_at' => now(),
                                'transportista' => $data['transportista'],
                                'transportista_contacto' => $data['transportista_contacto'],
                            ]);

                            $record->crearMovimientosEquipos();
                            self::notifyListoDespacho($record);
                        }),
                    Action::make('a_despachado')
                        ->label('Marcar despachado')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => in_array($record->estado, ['Solicitado', 'Preparacion']))
                        ->action(function (Pedido $record) {
                            try {
                                $record->validarDisponibilidadKit();
                            } catch (\Throwable $e) {
                                return Notification::make()
                                    ->title('Stock insuficiente para despachar kit')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }

                            $record->update(['estado' => 'Despachado']);
                            self::notifyRoles($record, 'Despachado');
                        }),
                    Action::make('a_entregado')
                        ->label('Marcar entregado')
                        ->icon('heroicon-o-archive-box')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->estado === 'Despachado')
                        ->action(function ($record) {
                            $record->update([
                                'estado' => 'Entregado',
                                'entregado_en_institucion_at' => $record->entregado_en_institucion_at ?: now(),
                            ]);
                            self::notifyRoles($record, 'Entregado');
                        }),
                    Action::make('a_devuelto')
                        ->label('Marcar devuelto')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('secondary')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => in_array($record->estado, ['Entregado', 'Despachado']))
                        ->action(function ($record) {
                            $record->update(['estado' => 'Devuelto']);
                            self::notifyRoles($record, 'Devuelto');
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

    protected static function notifyRoles($pedido, string $etapa): void
    {
        $recipients = User::role(['logistica', 'soporte_biomedico'])->get();
        if ($recipients->isNotEmpty()) {
            // Evitar duplicados recientes (misma etapa en 5 min)
            $recent = \Illuminate\Notifications\DatabaseNotification::query()
                ->where('type', PedidoTransitionNotification::class)
                ->whereJsonContains('data->pedido_id', $pedido->id)
                ->whereJsonContains('data->etapa', $etapa)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if (! $recent) {
                Notification::send($recipients, new PedidoTransitionNotification($pedido, $etapa));
            }
        }
    }

    protected static function notifyListoDespacho($pedido): void
    {
        $recipients = User::role(['logistica', 'soporte_biomedico'])->get();
        if ($recipients->isNotEmpty()) {
            $recent = \Illuminate\Notifications\DatabaseNotification::query()
                ->where('type', PedidoListoDespachoNotification::class)
                ->whereJsonContains('data->pedido_id', $pedido->id)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->exists();

            if (! $recent) {
                Notification::send($recipients, new PedidoListoDespachoNotification($pedido));
            }
        }
    }
}
