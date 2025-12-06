<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification;

class MisNotificaciones extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Notificaciones';
    protected static string|\UnitEnum|null $navigationGroup = 'Administración';
    protected string $view = 'filament.pages.mis-notificaciones';

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(auth()->user()->notifications()->getQuery()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('data.message')->label('Mensaje')->limit(60),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->limit(30),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Leída')
                    ->boolean()
                    ->trueIcon('heroicon-o-check')
                    ->falseIcon('heroicon-o-bell'),
            ])
            ->recordActions([
                Action::make('marcar_leida')
                    ->label('Marcar leída')
                    ->visible(fn ($record) => is_null($record->read_at))
                    ->action(fn (DatabaseNotification $record) => $record->markAsRead()),
            ]);
    }
}
