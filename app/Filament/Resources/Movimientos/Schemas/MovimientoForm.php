<?php

namespace App\Filament\Resources\Movimientos\Schemas;

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
                TextInput::make('nombre')
                    ->required(),
                DateTimePicker::make('fecha_salida')
                    ->required(),
                DateTimePicker::make('fecha_retorno'),
                Select::make('estado_mov')
                    ->options([
            'Programado' => 'Programado',
            'En uso' => 'En uso',
            'Devuelto' => 'Devuelto',
            'Observado' => 'Observado',
        ])
                    ->default('Programado')
                    ->required(),
                Select::make('motivo')
                    ->options([
            'Cirugia' => 'Cirugia',
            'Prestamo' => 'Prestamo',
            'Consignacion' => 'Consignacion',
            'Mantenimiento' => 'Mantenimiento',
            'Demostracion' => 'Demostracion',
        ])
                    ->default('Cirugia')
                    ->required(),
                Select::make('servicio')
                    ->options([
            'Neuro' => 'Neuro',
            'Columna' => 'Columna',
            'Maxilofacial' => 'Maxilofacial',
            'OTORRINO' => 'OTORRINO',
            'Otro' => 'Otro',
        ])
                    ->default('Neuro')
                    ->required(),
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
