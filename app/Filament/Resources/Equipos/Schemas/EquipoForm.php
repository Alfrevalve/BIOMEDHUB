<?php

namespace App\Filament\Resources\Equipos\Schemas;

use App\Enums\EquipoEstado;
use App\Enums\EquipoTipo;
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
                    ->options(EquipoTipo::options())
                    ->default(EquipoTipo::Craneo->value)
                    ->required(),
                Select::make('estado_actual')
                    ->options(EquipoEstado::options())
                    ->default(EquipoEstado::Disponible->value)
                    ->required(),
                Select::make('institucion_id')
                    ->relationship('institucion', 'nombre')
                    ->label('Institución')
                    ->searchable(),
                TextInput::make('marca_modelo'),
                TextInput::make('serie'),
                TextInput::make('responsable_actual'),
                Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }
}
