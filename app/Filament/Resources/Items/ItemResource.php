<?php

namespace App\Filament\Resources\Items;

use App\Filament\Resources\Items\Pages\CreateItem;
use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Pages\ListItems;
use App\Models\Item;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('sku')
                ->label('SKU')
                ->unique(ignoreRecord: true)
                ->required(),
            TextInput::make('nombre')
                ->label('Nombre')
                ->required(),
            TextInput::make('tipo')
                ->label('Tipo')
                ->placeholder('Fresa, Adaptador, etc.'),
            TextInput::make('stock_total')
                ->label('Stock total')
                ->numeric()
                ->default(0),
            Textarea::make('descripcion')
                ->label('Descripcion')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('tipo')->label('Tipo')->badge(),
                TextColumn::make('stock_total')->label('Stock')->sortable(),
                TextColumn::make('stock_reservado')->label('Reservado')->sortable(),
                TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->action(function (Item $record) {
                        if ($record->reservas()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar: tiene reservas asociadas')
                                ->danger()
                                ->send();

                            return;
                        }

                        if ($record->kitItems()->exists()) {
                            Notification::make()
                                ->title('No se puede eliminar: forma parte de un kit')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Item eliminado')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $blocked = [];
                            $deleted = 0;

                            foreach ($records as $record) {
                                /** @var Item $record */
                                if ($record->reservas()->exists() || $record->kitItems()->exists()) {
                                    $blocked[] = $record->sku;
                                    continue;
                                }

                                $record->delete();
                                $deleted++;
                            }

                            if ($deleted > 0) {
                                Notification::make()
                                    ->title("{$deleted} items eliminados")
                                    ->success()
                                    ->send();
                            }

                            if (! empty($blocked)) {
                                Notification::make()
                                    ->title('No se pudieron eliminar algunos items por relaciones (reservas/kits)')
                                    ->body('SKU: ' . implode(', ', $blocked))
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItems::route('/'),
            'create' => CreateItem::route('/create'),
            'edit' => EditItem::route('/{record}/edit'),
        ];
    }
}
