<?php

namespace App\Filament\Resources\Pedidos\Schemas;

use App\Enums\PedidoEstado;
use App\Enums\PedidoPrioridad;
use App\Models\ItemKit;
use App\Models\Item;
use App\Models\Equipo;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PedidoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('cirugia_id')
                    ->relationship('cirugia', 'nombre')
                    ->required(),
                Select::make('item_kit_id')
                    ->label('Kit de materiales')
                    ->options(ItemKit::query()->pluck('nombre', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('material_detalle', [])),
                Placeholder::make('kit_disponibilidad')
                    ->label('Disponibilidad del kit')
                    ->content(function (callable $get) {
                        $kitId = $get('item_kit_id');
                        if (! $kitId) {
                            return 'Selecciona un kit para ver sus componentes y stock.';
                        }

                        $kit = ItemKit::with(['items.item'])->find($kitId);
                        if (! $kit) {
                            return 'Kit no encontrado.';
                        }

                        $rows = [];
                        foreach ($kit->items as $kitItem) {
                            $item = $kitItem->item;
                            if (! $item) {
                                continue;
                            }
                            $disp = $item->disponible();
                            $rows[] = "{$item->sku} • requiere {$kitItem->cantidad} (disp: {$disp})";
                        }

                        return empty($rows)
                            ? 'El kit no tiene items definidos.'
                            : implode("\n", $rows);
                    })
                    ->columnSpanFull()
                    ->hint('Se valida stock y se reserva automáticamente al crear.'),
                Repeater::make('material_detalle')
                    ->label('Materiales')
                    ->schema([
                        Select::make('item_id')
                            ->label('Item')
                            ->searchable()
                            ->options(function (callable $get) {
                                $seleccionados = collect($get('material_detalle') ?? [])
                                    ->pluck('item_id')
                                    ->filter()
                                    ->all();

                                return Item::query()
                                    ->where(function ($q) use ($seleccionados) {
                                        $q->whereRaw('COALESCE(stock_total,0) - COALESCE(stock_reservado,0) > 0');
                                        if (! empty($seleccionados)) {
                                            $q->orWhereIn('id', $seleccionados);
                                        }
                                    })
                                    ->orderBy('nombre')
                                    ->get()
                                    ->mapWithKeys(fn (Item $i) => [
                                        $i->id => "{$i->nombre} ({$i->sku}) - disp: {$i->disponible()}",
                                    ])
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }
                                $item = Item::find($state);
                                $set('descripcion', $item?->nombre ?? '');
                            })
                            ->required()
                            ->helperText('Solo items con stock disponible y no reservados en otra cirugia.'),
                        TextInput::make('descripcion')
                            ->label('Descripcion')
                            ->readOnly()
                            ->dehydrated(false)
                            ->placeholder('Se autocompleta al elegir item'),
                        TextInput::make('cantidad')
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->maxValue(fn (callable $get) => optional(Item::find($get('item_id')))->disponible())
                            ->required(),
                    ])
                    ->columns(2)
                    ->collapsible(),
                Repeater::make('equipo_detalle')
                    ->label('Equipos')
                    ->schema([
                        Select::make('equipo_id')
                            ->label('Equipo')
                            ->searchable()
                            ->options(function (callable $get) {
                                $cirugiaId = $get('cirugia_id');
                                $seleccionados = collect($get('equipo_detalle') ?? [])
                                    ->pluck('equipo_id')
                                    ->filter()
                                    ->all();

                                return Equipo::query()
                                    ->select('id', 'nombre', 'serie', 'codigo_interno')
                                    ->when($cirugiaId, function ($q) use ($cirugiaId) {
                                        $q->whereNotIn('id', function ($sub) use ($cirugiaId) {
                                            $sub->select('equipo_id')
                                                ->from('movimientos')
                                                ->whereIn('estado_mov', ['Programado', 'En uso'])
                                                ->whereNotNull('cirugia_id')
                                                ->where('cirugia_id', '!=', $cirugiaId);
                                        });
                                    }, function ($q) {
                                        $q->whereNotIn('id', function ($sub) {
                                            $sub->select('equipo_id')
                                                ->from('movimientos')
                                                ->whereIn('estado_mov', ['Programado', 'En uso']);
                                        });
                                    })
                                    ->when(! empty($seleccionados), fn ($q) => $q->orWhereIn('id', $seleccionados))
                                    ->orderBy('nombre')
                                    ->get()
                                    ->mapWithKeys(function (Equipo $eq) {
                                        $badge = $eq->serie ?: $eq->codigo_interno;
                                        return [
                                            $eq->id => $badge
                                                ? "{$eq->nombre} ({$badge})"
                                                : $eq->nombre,
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    $set('codigo', null);
                                    return;
                                }

                                $eq = Equipo::find($state);
                                $set('codigo', $eq?->serie ?? $eq?->codigo_interno ?? '');
                            })
                            ->required()
                            ->helperText('Solo equipos libres (no programados/en uso en otra cirugia).'),
                        TextInput::make('codigo')
                            ->label('Codigo / serie')
                            ->maxLength(100)
                            ->readOnly()
                            ->placeholder('Se autocompleta al elegir equipo'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                TextInput::make('codigo_pedido')
                    ->hint('Se genera automaticamente si se deja vacio')
                    ->unique(ignoreRecord: true),
                Placeholder::make('estado_observado')
                    ->label('Estado del pedido')
                    ->content(fn (callable $get) => $get('estado') === 'Observado'
                        ? 'Este pedido está en Observado por falta de stock o error al reservar. Revisa disponibilidad y vuelve a guardar.'
                        : null)
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) => $get('estado') !== 'Observado')
                    ->extraAttributes(['style' => 'color:#b45309;background:#fef3c7;border:1px solid #fcd34d;padding:8px 12px;border-radius:8px;']),
                DatePicker::make('fecha'),
                DateTimePicker::make('fecha_entrega'),
                DateTimePicker::make('listo_despacho_at')
                    ->label('Listo para despacho')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('entregado_en_institucion_at')
                    ->label('Entregado en institucion')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('transportista'),
                TextInput::make('transportista_contacto')
                    ->label('Contacto transportista'),
                Select::make('estado')
                    ->options(PedidoEstado::options())
                    ->default('Solicitado')
                    ->required(),
                Select::make('prioridad')
                    ->options(PedidoPrioridad::options())
                    ->default('Alta')
                    ->required(),
                TextInput::make('entrega_a'),
                TextInput::make('responsable'),
            ]);
    }
}
