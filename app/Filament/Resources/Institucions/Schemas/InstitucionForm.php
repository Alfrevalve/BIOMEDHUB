<?php

namespace App\Filament\Resources\Institucions\Schemas;

use App\Enums\InstitucionTipo;
use Filament\Forms\Components\DatePicker;
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
                    ->label('Institucion')
                    ->required(),
                TextInput::make('codigo_unico')
                    ->label('Codigo unico'),
                TextInput::make('nombre_establecimiento')
                    ->label('Nombre del establecimiento'),
                TextInput::make('clasificacion')
                    ->label('Clasificacion'),
                Select::make('tipo')
                    ->options(InstitucionTipo::options())
                    ->default(InstitucionTipo::Publica->value)
                    ->required(),
                TextInput::make('departamento'),
                TextInput::make('provincia'),
                TextInput::make('distrito'),
                TextInput::make('ubigeo')->maxLength(10),
                TextInput::make('direccion'),
                TextInput::make('ciudad'),
                TextInput::make('codigo_disa')->label('Codigo DISA'),
                TextInput::make('codigo_red')->label('Codigo Red'),
                TextInput::make('codigo_microrred')->label('Codigo Microrred'),
                TextInput::make('disa'),
                TextInput::make('red'),
                TextInput::make('microrred'),
                TextInput::make('codigo_ue')->label('Codigo UE'),
                TextInput::make('unidad_ejecutora')->label('Unidad Ejecutora'),
                TextInput::make('categoria'),
                TextInput::make('telefono'),
                TextInput::make('contacto'),
                TextInput::make('tipo_doc_categorizacion')->label('Tipo Doc. Categorizacion'),
                TextInput::make('nro_doc_categorizacion')->label('Nro. Doc. Categorizacion'),
                TextInput::make('horario'),
                DatePicker::make('inicio_actividad')->label('Inicio de actividad'),
                TextInput::make('director_medico')->label('Director medico'),
                TextInput::make('estado_institucion')->label('Estado institucion'),
                TextInput::make('situacion'),
                TextInput::make('condicion'),
                TextInput::make('inspeccion'),
                TextInput::make('norte')->numeric(),
                TextInput::make('este')->numeric(),
                TextInput::make('cota')->numeric(),
                TextInput::make('camas')->numeric(),
            ]);
    }
}
