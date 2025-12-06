<?php

namespace App\Filament\Resources\ItemKits;

use App\Filament\Resources\ItemKits\Pages\CreateItemKit;
use App\Filament\Resources\ItemKits\Pages\EditItemKit;
use App\Filament\Resources\ItemKits\Pages\ListItemKits;
use App\Models\Item;
use App\Models\ItemKit;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemKitResource extends Resource
{
    protected static ?string $model = ItemKit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventario';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nombre')
                ->label('Nombre')
                ->required(),
            TextInput::make('codigo')
                ->label('Código')
                ->unique(ignoreRecord: true)
                ->required(),
            Textarea::make('descripcion')
                ->label('Descripción')
                ->columnSpanFull(),
            Repeater::make('items')
                ->label('Composición')
                ->relationship()
                ->schema([
                    Select::make('item_id')
                        ->label('Ítem')
                        ->options(Item::query()->pluck('nombre', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('cantidad')
                        ->label('Cantidad')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->required(),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('Código')->searchable(),
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('items_count')->counts('items')->label('Ítems'),
                TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItemKits::route('/'),
            'create' => CreateItemKit::route('/create'),
            'edit' => EditItemKit::route('/{record}/edit'),
        ];
    }
}
