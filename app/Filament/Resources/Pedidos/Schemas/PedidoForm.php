<?php

namespace App\Filament\Resources\Pedidos\Schemas;

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
                    ->relationship('cirugia', 'id')
                    ->required(),
                TextInput::make('codigo_pedido')
                    ->required(),
                DatePicker::make('fecha'),
                DateTimePicker::make('fecha_entrega'),
                Select::make('estado')
                    ->options([
            'Solicitado' => 'Solicitado',
            'Preparacion' => 'Preparacion',
            'Despachado' => 'Despachado',
            'Entregado' => 'Entregado',
            'Devuelto' => 'Devuelto',
            'Anulado' => 'Anulado',
            'Observado' => 'Observado',
        ])
                    ->default('Solicitado')
                    ->required(),
                Select::make('prioridad')
                    ->options(['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja'])
                    ->default('Alta')
                    ->required(),
                TextInput::make('entrega_a'),
                TextInput::make('responsable'),
            ]);
    }
}
