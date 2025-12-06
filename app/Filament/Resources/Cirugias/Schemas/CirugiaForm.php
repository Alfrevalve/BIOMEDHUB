<?php

namespace App\Filament\Resources\Cirugias\Schemas;

use App\Enums\CirugiaEstado;
use App\Enums\CirugiaTipo;
use App\Models\User;
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
                    ->options(CirugiaEstado::options())
                    ->default('Pendiente')
                    ->required(),
                TextInput::make('cirujano_principal'),
                Select::make('instrumentista_id')
                    ->label('Instrumentista')
                    ->options(fn () => User::role('instrumentista')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $user = $state ? User::find($state) : null;
                        if ($user) {
                            $set('instrumentista_asignado', $user->name);
                        }
                    }),
                TextInput::make('instrumentista_asignado')
                    ->label('Instrumentista (texto)')
                    ->helperText('Se autocompleta al elegir usuario'),
                Select::make('tipo')
                    ->options(CirugiaTipo::options())
                    ->default('Craneo')
                    ->required(),
                Toggle::make('crear_pedido_auto')
                    ->default(true)
                    ->required(),
                TextInput::make('paciente_codigo')
                    ->label('Paciente (nombre y apellido)')
                    ->placeholder('Ej: Juan Perez'),
                TextInput::make('monto_soles')
                    ->numeric(),
            ]);
    }
}
