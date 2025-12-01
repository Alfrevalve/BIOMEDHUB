<?php

namespace App\Filament\Resources\Movimientos;

use App\Filament\Resources\Movimientos\Pages\CreateMovimiento;
use App\Filament\Resources\Movimientos\Pages\EditMovimiento;
use App\Filament\Resources\Movimientos\Pages\ListMovimientos;
use App\Filament\Resources\Movimientos\Schemas\MovimientoForm;
use App\Filament\Resources\Movimientos\Tables\MovimientosTable;
use App\Models\Movimiento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MovimientoResource extends Resource
{
    protected static ?string $model = Movimiento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return MovimientoForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MovimientosTable::configure($table);
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
            'index' => ListMovimientos::route('/'),
            'create' => CreateMovimiento::route('/create'),
            'edit' => EditMovimiento::route('/{record}/edit'),
        ];
    }
}
