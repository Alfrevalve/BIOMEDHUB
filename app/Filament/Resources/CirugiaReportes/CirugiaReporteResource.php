<?php

namespace App\Filament\Resources\CirugiaReportes;

use App\Filament\Resources\CirugiaReportes\Pages\ListCirugiaReportes;
use App\Models\CirugiaReporte;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CirugiaReporteResource extends Resource
{
    protected static ?string $model = CirugiaReporte::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Operativo';
    protected static ?string $modelLabel = 'Reporte de cirugia';
    protected static ?string $pluralModelLabel = 'Reportes de cirugia';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugia')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('institucion')
                    ->label('Institucion')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('paciente')
                    ->label('Paciente')
                    ->limit(40),
                TextColumn::make('hora_programada')
                    ->label('Hora programada')
                    ->dateTime(),
                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hora_termino')
                    ->label('Termino')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('consumo')
                    ->label('Consumo')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
                TextColumn::make('evidencia_path')
                    ->label('Evidencia')
                    ->formatStateUsing(fn ($state) => $state ? 'Ver' : 'Sin evidencia')
                    ->icon(fn ($state) => $state ? 'heroicon-o-photo' : 'heroicon-o-exclamation-circle')
                    ->url(fn ($record) => $record->evidencia_path ? \Storage::disk('public')->url($record->evidencia_path) : null, shouldOpenInNewTab: true)
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('fecha')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('desde'),
                        \Filament\Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
                SelectFilter::make('institucion')
                    ->label('Institucion')
                    ->options(fn () => CirugiaReporte::query()
                        ->whereNotNull('institucion')
                        ->distinct()
                        ->orderBy('institucion')
                        ->pluck('institucion', 'institucion')
                        ->toArray()),
                SelectFilter::make('estado_pedido')
                    ->label('Estado de pedido')
                    ->options([
                        'Solicitado' => 'Solicitado',
                        'Preparacion' => 'Preparacion',
                        'Despachado' => 'Despachado',
                        'Entregado' => 'Entregado',
                        'Devuelto' => 'Devuelto',
                        'Anulado' => 'Anulado',
                        'Observado' => 'Observado',
                    ])
                    ->query(function ($query, $state) {
                        return $query->whereHas('cirugia.pedidos', fn ($q) => $q->where('estado', $state));
                    }),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCirugiaReportes::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'auditoria', 'soporte_biomedico', 'almacen', 'facturacion', 'comercial']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canForceDelete($record): bool
    {
        return false;
    }

    public static function canRestore($record): bool
    {
        return false;
    }
}
