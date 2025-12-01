<?php

namespace App\Filament\Resources\Equipos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EquipoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                TextInput::make('codigo_interno'),
                Select::make('tipo')
                    ->options([
            'Craneo' => 'Craneo',
            'Columna' => 'Columna',
            'Motor' => 'Motor',
            'Consola' => 'Consola',
            'Fresas' => 'Fresas',
        ])
                    ->default('Craneo')
                    ->required(),
                Select::make('estado_actual')
                    ->options([
            'Disponible' => 'Disponible',
            'En cirugia' => 'En cirugia',
            'Asignado' => 'Asignado',
            'En mantenimiento' => 'En mantenimiento',
            'En transito' => 'En transito',
        ])
                    ->default('Disponible')
                    ->required(),
                TextInput::make('institucion_id')
                    ->numeric(),
                TextInput::make('marca_modelo'),
                TextInput::make('serie'),
                TextInput::make('responsable_actual'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }
}
