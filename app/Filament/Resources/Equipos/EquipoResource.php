<?php

namespace App\Filament\Resources\Equipos;

use App\Filament\Resources\Equipos\Pages\CreateEquipo;
use App\Filament\Resources\Equipos\Pages\EditEquipo;
use App\Filament\Resources\Equipos\Pages\ListEquipos;
use App\Filament\Resources\Equipos\Schemas\EquipoForm;
use App\Filament\Resources\Equipos\Tables\EquiposTable;
use App\Models\Equipo;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EquipoResource extends Resource
{
    protected static ?string $model = Equipo::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $recordTitleAttribute = 'nombre';

    public static function form(Schema $schema): Schema
    {
        return EquipoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EquiposTable::configure($table);
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
            'index' => ListEquipos::route('/'),
            'create' => CreateEquipo::route('/create'),
            'edit' => EditEquipo::route('/{record}/edit'),
        ];
    }

    protected static function canManage(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'soporte_biomedico']) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'auditoria', 'soporte_biomedico', 'almacen']) ?? false;
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
