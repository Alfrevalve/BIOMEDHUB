<?php

namespace App\Filament\Resources\Institucions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InstitucionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nombre')
                    ->required(),
                Select::make('tipo')
                    ->options(['Publica' => 'Publica', 'Privada' => 'Privada', 'Militar' => 'Militar', 'ONG' => 'ONG'])
                    ->default('Publica')
                    ->required(),
                TextInput::make('ciudad'),
                TextInput::make('direccion'),
                TextInput::make('contacto'),
            ]);
    }
}
