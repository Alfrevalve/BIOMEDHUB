<?php

namespace App\Filament\Resources\Cirugias\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CirugiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('institucion_id')
                    ->relationship('institucion', 'nombre')
                    ->searchable()
                    ->required(),
                TextInput::make('nombre')
                    ->required(),
                DateTimePicker::make('fecha_programada')
                    ->required(),
                Select::make('estado')
                    ->options([
            'Pendiente' => 'Pendiente',
            'En curso' => 'En curso',
            'Cerrada' => 'Cerrada',
            'Reprogramada' => 'Reprogramada',
            'Cancelada' => 'Cancelada',
        ])
                    ->default('Pendiente')
                    ->required(),
                TextInput::make('cirujano_principal'),
                TextInput::make('instrumentista_asignado'),
                Select::make('tipo')
                    ->options([
            'Craneo' => 'Craneo',
            'Columna' => 'Columna',
            'Tumor' => 'Tumor',
            'Pediatrica' => 'Pediatrica',
            'Otro' => 'Otro',
        ])
                    ->default('Craneo')
                    ->required(),
                Toggle::make('crear_pedido_auto')
                    ->default(true)
                    ->required(),
                TextInput::make('paciente_codigo'),
                TextInput::make('monto_soles')
                    ->numeric(),
            ]);
    }
}
