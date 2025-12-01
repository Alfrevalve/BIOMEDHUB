<?php

namespace App\Filament\Resources\Cirugias;

use App\Filament\Resources\Cirugias\Pages\CreateCirugia;
use App\Filament\Resources\Cirugias\Pages\EditCirugia;
use App\Filament\Resources\Cirugias\Pages\ListCirugias;
use App\Filament\Resources\Cirugias\Schemas\CirugiaForm;
use App\Filament\Resources\Cirugias\Tables\CirugiasTable;
use App\Models\Cirugia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CirugiaResource extends Resource
{
    protected static ?string $model = Cirugia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;

    protected static ?string $recordTitleAttribute = 'paciente';

    public static function form(Schema $schema): Schema
    {
        return CirugiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CirugiasTable::configure($table);
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
            'index' => ListCirugias::route('/'),
            'create' => CreateCirugia::route('/create'),
            'edit' => EditCirugia::route('/{record}/edit'),
        ];
    }
}
