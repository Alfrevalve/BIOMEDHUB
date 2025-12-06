<?php

namespace App\Filament\Resources\Pedidos;

use App\Filament\Resources\Pedidos\Pages\CreatePedido;
use App\Filament\Resources\Pedidos\Pages\EditPedido;
use App\Filament\Resources\Pedidos\Pages\ListPedidos;
use App\Filament\Resources\Pedidos\Schemas\PedidoForm;
use App\Filament\Resources\Pedidos\Tables\PedidosTable;
use App\Models\Pedido;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return PedidoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PedidosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPedidos::route('/'),
            'create' => CreatePedido::route('/create'),
            'edit' => EditPedido::route('/{record}/edit'),
        ];
    }

    protected static function canManage(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica']) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'auditoria']) ?? false;
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
