<?php

namespace App\Filament\Resources\CirugiaReportes;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use App\Filament\Resources\CirugiaReportes\Pages\CreateCirugiaReporte;
use App\Filament\Resources\CirugiaReportes\Pages\EditCirugiaReporte;
use App\Filament\Resources\CirugiaReportes\Pages\ListCirugiaReportes;
use App\Filament\Resources\CirugiaReportes\Pages\ViewCirugiaReporte;
use App\Models\Cirugia;
use App\Models\CirugiaReporte;
use BackedEnum;
use UnitEnum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CirugiaReporteResource extends Resource
{
    protected static ?string $model = CirugiaReporte::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Operativo';
    protected static ?string $modelLabel = 'Reporte de cirugia';
    protected static ?string $pluralModelLabel = 'Reportes de cirugia';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('cirugia_id')
                ->label('Cirugia planificada')
                ->searchable()
                ->getSearchResultsUsing(function (string $search) {
                    $user = auth()->user();
                    $query = Cirugia::query()
                        ->noCanceladas()
                        ->with('institucion')
                        ->when(! self::userIsAdminLike($user), fn ($q) => $q->where('instrumentista_id', $user?->id))
                        ->when(
                            $search,
                            fn ($q) => $q->where(function ($sub) use ($search) {
                                $sub->where('nombre', 'like', "%{$search}%")
                                    ->orWhereHas('institucion', fn ($qi) => $qi->where('nombre', 'like', "%{$search}%"))
                                    ->orWhereDate('fecha_programada', $search);
                            })
                        )
                        ->orderByDesc('fecha_programada')
                        ->limit(20);

                    return $query->get()
                        ->mapWithKeys(fn (Cirugia $c) => [
                            $c->id => sprintf(
                                '%s | %s | %s',
                                $c->fecha_programada?->format('d/m H:i') ?? 'Sin fecha',
                                $c->institucion?->nombre ?? 'Sin institucion',
                                $c->nombre ?? 'Sin nombre'
                            ),
                        ])
                        ->toArray();
                })
                ->getOptionLabelUsing(function ($value) {
                    if (! $value) {
                        return null;
                    }

                    $c = Cirugia::query()->with('institucion')->find($value);
                    if (! $c) {
                        return null;
                    }

                    return sprintf(
                        '%s | %s | %s',
                        $c->fecha_programada?->format('d/m H:i') ?? 'Sin fecha',
                        $c->institucion?->nombre ?? 'Sin institucion',
                        $c->nombre ?? 'Sin nombre'
                    );
                })
                ->required()
                ->reactive()
                ->helperText('Selecciona la cirugia programada; los campos se completan automaticamente.')
                ->afterStateUpdated(function ($state, callable $set) {
                    if (! $state) {
                        return;
                    }
                    $c = Cirugia::query()->with('institucion')->find($state);
                    if (! $c) {
                        return;
                    }
                    $set('institucion', $c->institucion?->nombre ?? '');
                    $set('paciente', $c->paciente_codigo ?? '');
                    $set('hora_programada', $c->fecha_programada);
                }),
            TextInput::make('institucion')
                ->label('Institucion')
                ->required()
                ->maxLength(255),
            TextInput::make('paciente')
                ->label('Paciente')
                ->maxLength(255),
            DateTimePicker::make('hora_programada')
                ->label('Hora programada')
                ->required(),
            DateTimePicker::make('hora_inicio')
                ->label('Hora de inicio'),
            DateTimePicker::make('hora_termino')
                ->label('Hora de termino'),
            Textarea::make('consumo')
                ->label('Consumo')
                ->rows(3),
            Textarea::make('notas')
                ->label('Notas')
                ->rows(3),
            FileUpload::make('evidencia_path')
                ->label('Evidencia')
                ->directory('evidencias-cirugias')
                ->disk('public')
                ->visibility('public')
                ->preserveFilenames()
                ->acceptedFileTypes(['image/*', 'application/pdf']),
        ])->columns(2);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->columns([
                TextColumn::make('cirugia.nombre')
                    ->label('Cirugia')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('institucion')
                    ->label('Institucion')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('paciente')
                    ->label('Paciente')
                    ->limit(40),
                TextColumn::make('hora_programada')
                    ->label('Hora programada')
                    ->dateTime(),
                TextColumn::make('hora_inicio')
                    ->label('Inicio')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('hora_termino')
                    ->label('Termino')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('consumo')
                    ->label('Consumo')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
                TextColumn::make('evidencia_path')
                    ->label('Evidencia')
                    ->formatStateUsing(fn ($state) => $state ? 'Ver' : 'Sin evidencia')
                    ->icon(fn ($state) => $state ? 'heroicon-o-photo' : 'heroicon-o-exclamation-circle')
                    ->url(fn ($record) => $record->evidencia_path ? \Storage::disk('public')->url($record->evidencia_path) : null, shouldOpenInNewTab: true)
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime(),
            ])
            ->filters([
                Filter::make('fecha')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('desde'),
                        \Filament\Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'] ?? null, fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
                SelectFilter::make('institucion')
                    ->label('Institucion')
                    ->options(fn () => CirugiaReporte::query()
                        ->whereNotNull('institucion')
                        ->distinct()
                        ->orderBy('institucion')
                        ->pluck('institucion', 'institucion')
                        ->toArray()),
                SelectFilter::make('estado_pedido')
                    ->label('Estado de pedido')
                    ->options([
                        'Solicitado' => 'Solicitado',
                        'Preparacion' => 'Preparacion',
                        'Despachado' => 'Despachado',
                        'Entregado' => 'Entregado',
                        'Devuelto' => 'Devuelto',
                        'Anulado' => 'Anulado',
                        'Observado' => 'Observado',
                    ])
                    ->query(function ($query, $state) {
                        return $query->whereHas('cirugia.pedidos', fn ($q) => $q->where('estado', $state));
                    }),
            ])
            ->actions([
                Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record])),
                EditAction::make()
                    ->label('Editar')
                    ->url(fn ($record) => static::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([]);
    }

    protected static function userHasAllowedRole(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        $allowed = [
            'admin',
            'administrador',
            'administrator',
            'logistica',
            'auditoria',
            'soporte_biomedico',
            'almacen',
            'facturacion',
            'comercial',
            'instrumentista',
        ];

        return $user->getRoleNames()
            ->map(fn (string $role) => strtolower($role))
            ->contains(fn (string $role) => in_array($role, $allowed, true));
    }

    protected static function userIsAdminLike($user): bool
    {
        if (! $user) {
            return false;
        }

        $adminish = ['admin', 'administrador', 'administrator', 'logistica', 'auditoria', 'soporte_biomedico'];

        return $user->getRoleNames()
            ->map(fn (string $role) => strtolower($role))
            ->contains(fn (string $role) => in_array($role, $adminish, true));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCirugiaReportes::route('/'),
            'create' => CreateCirugiaReporte::route('/create'),
            'view' => ViewCirugiaReporte::route('/{record}'),
            'edit' => EditCirugiaReporte::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return self::userHasAllowedRole();
    }

    public static function canCreate(): bool
    {
        return self::userHasAllowedRole();
    }

    public static function canEdit($record): bool
    {
        return self::userHasAllowedRole();
    }

    public static function canDelete($record): bool
    {
        return self::userHasAllowedRole();
    }

    public static function canForceDelete($record): bool
    {
        return self::userHasAllowedRole();
    }

    public static function canRestore($record): bool
    {
        return self::userHasAllowedRole();
    }
}

