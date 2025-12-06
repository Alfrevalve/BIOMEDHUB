<?php

namespace App\Filament\Resources\Pedidos\Tables;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use App\Models\User;
use App\Notifications\PedidoTransitionNotification;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugia')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('itemKit.nombre')
                    ->label('Kit')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    Action::make('a_despachado')
                        ->label('Marcar despachado')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => in_array($record->estado, ['Solicitado', 'Preparacion']))
                        ->action(function ($record) {
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
                            $record->update(['estado' => 'Entregado']);
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
            Notification::send($recipients, new PedidoTransitionNotification($pedido, $etapa));
        }
    }
}
