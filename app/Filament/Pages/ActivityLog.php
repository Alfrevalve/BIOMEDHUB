<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Auditoria';
    protected static string|\UnitEnum|null $navigationGroup = 'Administracion';
    protected string $view = 'filament.pages.activity-log';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin','administrador','auditoria']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('causer.name')->label('Usuario')->sortable(),
                Tables\Columns\TextColumn::make('log_name')->label('Log')->badge(),
                Tables\Columns\TextColumn::make('description')->label('Descripcion')->limit(80)->searchable(),
                Tables\Columns\TextColumn::make('subject_type')->label('Modelo')->limit(50)->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Log')
                    ->options(Activity::query()->distinct()->pluck('log_name', 'log_name')->filter()->all()),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->searchable()
            ->paginated([10, 25, 50]);
    }
}
