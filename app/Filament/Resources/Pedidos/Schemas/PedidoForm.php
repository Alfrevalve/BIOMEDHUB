<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use App\Models\ItemKit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cirugia_id')
                    ->relationship('cirugia', 'nombre')
                    ->required(),
                Select::make('item_kit_id')
                    ->label('Kit de materiales')
                    ->options(ItemKit::query()->pluck('nombre', 'id'))
                    ->searchable(),
                TextInput::make('codigo_pedido')
                    ->hint('Se genera automáticamente si se deja vacío')
                    ->unique(ignoreRecord: true),
                DatePicker::make('fecha'),
                DateTimePicker::make('fecha_entrega'),
                Select::make('estado')
                    ->options(PedidoEstado::options())
                    ->default('Solicitado')
                    ->required(),
                Select::make('prioridad')
                    ->options(PedidoPrioridad::options())
                    ->default('Alta')
                    ->required(),
                TextInput::make('entrega_a'),
                TextInput::make('responsable'),
            ]);
    }
}
