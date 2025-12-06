<?php

namespace App\Filament\Resources\Movimientos\Schemas;

use App\Enums\MovimientoEstado;
use App\Enums\MovimientoMotivo;
use App\Enums\MovimientoServicio;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MovimientoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('equipo_id')
                    ->relationship('equipo', 'nombre')
                    ->searchable()
                    ->required(),
                Select::make('institucion_id')
                    ->relationship('institucion', 'nombre')
                    ->searchable(),
                Select::make('cirugia_id')
                    ->relationship('cirugia', 'nombre')
                    ->searchable(),
                Select::make('pedido_id')
                    ->relationship('pedido', 'codigo_pedido')
                    ->searchable()
                    ->preload(),
                TextInput::make('nombre')
                    ->placeholder('Se autogenera si se deja vacio'),
                DateTimePicker::make('fecha_salida')
                    ->required(),
                DateTimePicker::make('fecha_retorno'),
                Select::make('estado_mov')
                    ->options(MovimientoEstado::options())
                    ->default(MovimientoEstado::Programado->value)
                    ->required(),
                Select::make('motivo')
                    ->options(MovimientoMotivo::options())
                    ->default(MovimientoMotivo::Cirugia->value)
                    ->required(),
                Select::make('servicio')
                    ->options(MovimientoServicio::options())
                    ->default(MovimientoServicio::Neuro->value)
                    ->required(),
                TextInput::make('transportista'),
                TextInput::make('transportista_contacto')
                    ->label('Contacto transportista'),
                TagsInput::make('material_enviado')
                    ->placeholder('Agregar item')
                    ->separator(',')
                    ->columnSpanFull(),
                TextInput::make('entregado_por'),
                TextInput::make('recibido_por'),
                TextInput::make('documento_soporte'),
            ]);
    }
}
