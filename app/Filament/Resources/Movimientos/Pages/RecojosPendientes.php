<?php

namespace App\Filament\Resources\Movimientos\Pages;

use App\Filament\Resources\Movimientos\MovimientoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class RecojosPendientes extends ListRecords
{
    protected static string $resource = MovimientoResource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Recojos pendientes';
    protected static string|\UnitEnum|null $navigationGroup = 'Operativo';

    public function getTitle(): string
    {
        return 'Recojos pendientes';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('marcar_recogido')
                ->label('Marcar recogido')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (array $data, $livewire) {
                    $selected = $livewire->getSelectedTableRecords();
                    foreach ($selected as $record) {
                        $record->update([
                            'estado_mov' => 'Devuelto',
                            'fecha_retorno' => $record->fecha_retorno ?: now(),
                            'recogida_solicitada_at' => $record->recogida_solicitada_at ?: now(),
                        ]);
                    }

                    $livewire->notify('success', 'Recojo marcado para ' . count($selected) . ' movimiento(s).');
                    $livewire->clearTableSelection();
                })
                ->visible(fn ($livewire) => $livewire->getSelectedTableRecords()->count() > 0),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->whereNotNull('recogida_solicitada_at')
            ->whereIn('estado_mov', ['Devuelto', 'En uso', 'Programado'])
            ->orderByDesc('recogida_solicitada_at');
    }
}
