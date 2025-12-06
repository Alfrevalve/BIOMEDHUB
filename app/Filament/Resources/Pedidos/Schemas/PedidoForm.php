<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use App\Models\ItemKit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
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
                Repeater::make('material_detalle')
                    ->label('Materiales')
                    ->schema([
                        TextInput::make('descripcion')
                            ->label('Descripcion')
                            ->required(),
                        TextInput::make('cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Repeater::make('equipo_detalle')
                    ->label('Equipos')
                    ->schema([
                        TextInput::make('equipo')
                            ->required(),
                        TextInput::make('codigo')
                            ->label('Codigo / serie')
                            ->maxLength(100),
                    ])
                    ->columns(2)
                    ->collapsible(),
                TextInput::make('codigo_pedido')
                    ->hint('Se genera automaticamente si se deja vacio')
                    ->unique(ignoreRecord: true),
                DatePicker::make('fecha'),
                DateTimePicker::make('fecha_entrega'),
                DateTimePicker::make('listo_despacho_at')
                    ->label('Listo para despacho')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('entregado_en_institucion_at')
                    ->label('Entregado en institucion')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('transportista'),
                TextInput::make('transportista_contacto')
                    ->label('Contacto transportista'),
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
