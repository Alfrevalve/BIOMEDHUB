<?php

namespace App\Filament\Pages;

use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use BackedEnum;
use UnitEnum;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class Mensajes extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected string $view = 'filament.pages.mensajes';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';
    protected static string|UnitEnum|null $navigationGroup = 'Administracion';
    protected static ?string $navigationLabel = 'Mensajes';
    protected static ?string $title = 'Mensajes internos';

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function table(Table $table): Table
    {
        $userId = auth()->id();

        return $table
            ->query(
                Message::query()
                    ->with(['sender'])
                    ->where('recipient_id', $userId)
                    ->latest()
            )
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->label('Asunto')
                    ->weight('semibold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sender.name')
                    ->label('De')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Leido')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope-open')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('estado')
                    ->label('Leido')
                    ->trueLabel('Leidos')
                    ->falseLabel('No leidos')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->headerActions([
                Action::make('redactar')
                    ->label('Nuevo mensaje')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->form($this->composeForm())
                    ->action(function (array $data) {
                        $sender = auth()->user();
                        $recipient = User::find($data['recipient_id'] ?? null);
                        if (! $sender || ! $recipient) {
                            return;
                        }

                        $message = Message::create([
                            'sender_id' => $sender->id,
                            'recipient_id' => $recipient->id,
                            'subject' => $data['subject'],
                            'body' => $data['body'],
                        ]);

                        Notification::send($recipient, new MessageReceivedNotification($message));
                    })
                    ->closeModalByClickingAway(false),
                Action::make('marcar_todo')
                    ->label('Marcar todo leído')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function () use ($userId) {
                        Message::where('recipient_id', $userId)
                            ->whereNull('read_at')
                            ->update(['read_at' => now()]);
                    }),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Abrir')
                    ->icon('heroicon-o-eye')
                    ->action(function (Message $record) {
                        $record->markAsRead();
                    })
                    ->modalHeading(fn (Message $record) => $record->subject)
                    ->modalContent(fn (Message $record) => view('filament.pages.partials.message-modal', [
                        'message' => $record->load('sender'),
                    ]))
                    ->modalSubmitAction(false),
                Action::make('marcar_leido')
                    ->label('Marcar leido')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Message $record) => $record->read_at === null)
                    ->action(fn (Message $record) => $record->markAsRead()),
                Action::make('marcar_no_leido')
                    ->label('Marcar no leido')
                    ->icon('heroicon-o-envelope')
                    ->visible(fn (Message $record) => $record->read_at !== null)
                    ->action(fn (Message $record) => $record->update(['read_at' => null])),
                Actions\DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\BulkAction::make('marcar_leidos')
                        ->label('Marcar como leidos')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->markAsRead()),
                    Actions\BulkAction::make('marcar_no_leidos')
                        ->label('Marcar como no leidos')
                        ->icon('heroicon-o-envelope')
                        ->action(fn ($records) => $records->each->update(['read_at' => null])),
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Sin mensajes')
            ->emptyStateDescription('Cuando recibas mensajes internos apareceran aqui.');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = auth()->user()?->receivedMessages()->whereNull('read_at')->count();
        return $count ? (string) $count : null;
    }

    protected function composeForm(): array
    {
        return [
            Select::make('recipient_id')
                ->label('Para')
                ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('subject')
                ->label('Asunto')
                ->required()
                ->maxLength(200),
            RichEditor::make('body')
                ->label('Mensaje')
                ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                ->required()
                ->columnSpanFull(),
        ];
    }
}
