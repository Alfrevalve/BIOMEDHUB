<?php

namespace App\Filament\Resources\Institucions;

use App\Filament\Resources\Institucions\Pages\CreateInstitucion;
use App\Filament\Resources\Institucions\Pages\EditInstitucion;
use App\Filament\Resources\Institucions\Pages\ListInstitucions;
use App\Filament\Resources\Institucions\Schemas\InstitucionForm;
use App\Filament\Resources\Institucions\Tables\InstitucionsTable;
use App\Models\Institucion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InstitucionResource extends Resource
{
    protected static ?string $model = Institucion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static ?string $recordTitleAttribute = 'nombre';
    protected static ?string $modelLabel = 'Institucion';
    protected static ?string $pluralModelLabel = 'Instituciones';
    protected static ?string $navigationLabel = 'Instituciones';

    public static function form(Schema $schema): Schema
    {
        return InstitucionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstitucionsTable::configure($table);
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
            'index' => ListInstitucions::route('/'),
            'create' => CreateInstitucion::route('/create'),
            'edit' => EditInstitucion::route('/{record}/edit'),
        ];
    }

    protected static function canManage(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'comercial']) ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'logistica', 'auditoria', 'comercial']) ?? false;
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
