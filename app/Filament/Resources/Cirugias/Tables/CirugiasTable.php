<?php

namespace App\Filament\Resources\Cirugias\Tables;

use App\Enums\CirugiaEstado;
use App\Enums\CirugiaTipo;
use App\Models\CirugiaReporte;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CirugiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_programada', 'asc')
            ->striped()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Cirugia')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Pendiente' => 'warning',
                        'En curso' => 'primary',
                        'Cerrada' => 'success',
                        'Reprogramada' => 'info',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('fecha_programada')
                    ->label('Fecha programada')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge(),
                TextColumn::make('institucion.nombre')
                    ->label('Institucion')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cirujano_principal')
                    ->label('Cirujano')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('instrumentista_asignado')
                    ->label('Instrumentista')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('paciente_codigo')
                    ->label('Paciente')
                    ->searchable(),
                IconColumn::make('crear_pedido_auto')
                    ->label('Pedido auto')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('monto_soles')
                    ->label('Monto S/')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options(CirugiaEstado::options()),
                SelectFilter::make('tipo')
                    ->options(CirugiaTipo::options()),
                Filter::make('rango_fecha')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $date) => $q->whereDate('fecha_programada', '>=', $date))
                            ->when($data['hasta'] ?? null, fn ($q, $date) => $q->whereDate('fecha_programada', '<=', $date));
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->tooltip('Editar cirugia'),
                    Action::make('reporte_cirugia')
                        ->label('Reporte de cirugia')
                        ->color('success')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->form([
                            DateTimePicker::make('hora_programada')
                                ->label('Hora programada')
                                ->default(fn ($record) => $record?->fecha_programada)
                                ->required(),
                            DateTimePicker::make('hora_inicio')
                                ->label('Hora de inicio')
                                ->default(fn () => now('America/Lima'))
                                ->required(),
                            DateTimePicker::make('hora_termino')
                                ->label('Hora de termino')
                                ->default(fn () => now('America/Lima')->addHours(2))
                                ->required(),
                            TextInput::make('paciente')
                                ->label('Paciente')
                                ->placeholder('Nombre y apellido')
                                ->default(fn ($record) => $record?->paciente_codigo)
                                ->required(),
                            Textarea::make('consumo')
                                ->label('Consumo / materiales usados')
                                ->placeholder('Ej: MR8-9BA50, MR8-9BA50D')
                                ->rows(3),
                            Textarea::make('notas')
                                ->label('Notas adicionales')
                                ->placeholder('Observaciones o incidencias')
                                ->rows(2),
                            FileUpload::make('evidencia_path')
                                ->label('Foto o evidencia')
                                ->image()
                                ->directory('reportes-cirugia')
                                ->disk('public'),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['estado' => CirugiaEstado::Cerrada->value]);

                            CirugiaReporte::create([
                                'cirugia_id'      => $record->id,
                                'institucion'     => $record->institucion?->nombre,
                                'paciente'        => $data['paciente'] ?? $record->paciente_codigo,
                                'hora_programada' => $data['hora_programada'] ?? $record->fecha_programada,
                                'hora_inicio'     => $data['hora_inicio'] ?? null,
                                'hora_termino'    => $data['hora_termino'] ?? null,
                                'consumo'         => $data['consumo'] ?? null,
                                'notas'           => $data['notas'] ?? null,
                                'evidencia_path'  => $data['evidencia_path'] ?? null,
                            ]);
                        })
                        ->closeModalByClickingAway(false)
                        ->modalWidth('lg'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
