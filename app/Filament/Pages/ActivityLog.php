<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLog extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Auditoría';
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected string $view = 'filament.pages.activity-log';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('causer.name')->label('Usuario')->sortable(),
                Tables\Columns\TextColumn::make('log_name')->label('Log')->badge(),
                Tables\Columns\TextColumn::make('description')->label('Descripción')->limit(50),
                Tables\Columns\TextColumn::make('subject_type')->label('Modelo')->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->options(Activity::query()->distinct()->pluck('log_name', 'log_name')->filter()->all()),
            ])
            ->paginated([10, 25, 50]);
    }
}
