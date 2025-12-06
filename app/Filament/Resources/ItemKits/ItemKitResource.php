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
use Illuminate\Support\Str;

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
                ->label('Codigo')
                ->unique(ignoreRecord: true)
                ->required(),
            Textarea::make('descripcion')
                ->label('Descripcion')
                ->columnSpanFull(),
            Repeater::make('items')
                ->label('Composicion')
                ->relationship()
                ->schema([
                    Select::make('item_id')
                        ->label('SKU / Item')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search): array {
                            return Item::query()
                                ->where('sku', 'like', "%{$search}%")
                                ->orWhere('nombre', 'like', "%{$search}%")
                                ->orderBy('sku')
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn (Item $item) => [
                                    $item->id => "{$item->sku} — " . Str::limit($item->nombre, 80),
                                ])
                                ->toArray();
                        })
                        ->getOptionLabelUsing(function ($value): ?string {
                            $item = Item::find($value);

                            return $item ? "{$item->sku} — {$item->nombre}" : null;
                        })
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
                TextColumn::make('codigo')->label('Codigo')->searchable(),
                TextColumn::make('nombre')->label('Nombre')->searchable(),
                TextColumn::make('items_count')->counts('items')->label('Items'),
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

    protected static function canManage(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'almacen']) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'almacen', 'auditoria']) ?? false;
    }

    public static function canCreate(): bool
    {
        return self::canManage();
    }

    public static function canEdit($record): bool
    {
        return self::canManage();
    }

    public static function canDelete($record): bool
    {
        return self::canManage();
    }

    public static function canForceDelete($record): bool
    {
        return self::canManage();
    }

    public static function canRestore($record): bool
    {
        return self::canManage();
    }
}
