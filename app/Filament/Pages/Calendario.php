<?php

namespace App\Filament\Pages;

use App\Models\Cirugia;
use App\Models\Pedido;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class Calendario extends Page
{
    protected string $view = 'filament.pages.calendario';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static string|UnitEnum|null $navigationGroup = 'Operativo';
    protected static ?string $navigationLabel = 'Calendario';
    protected static ?string $title = 'Calendario operativo';

    public ?string $eventsJson = null;

    public function mount(): void
    {
        $user = auth()->user();
        $isAdmin = $user?->hasRole('admin') ?? false;

        $events = [];

        $cirugias = Cirugia::query()
            ->when(! $isAdmin, fn ($q) => $q->where('instrumentista_id', $user?->id))
            ->whereNotNull('fecha_programada')
            ->with('institucion')
            ->get();

        foreach ($cirugias as $c) {
            $events[] = [
                'title' => 'Cirugía: ' . ($c->nombre ?? 'Sin nombre'),
                'start' => optional($c->fecha_programada)->toIso8601String(),
                'url' => route('filament.admin.resources.cirugias.edit', $c),
                'color' => '#0ea5e9',
                'extendedProps' => [
                    'institucion' => $c->institucion?->nombre,
                    'tipo' => 'Cirugía',
                ],
            ];
        }

        $pedidos = Pedido::query()
            ->whereNotNull('fecha_entrega')
            ->when(! $isAdmin, fn ($q) => $q->whereHas('cirugia', fn ($cq) => $cq->where('instrumentista_id', $user?->id)))
            ->with('cirugia')
            ->get();

        foreach ($pedidos as $p) {
            $events[] = [
                'title' => 'Pedido: ' . ($p->codigo_pedido ?? 'Pedido'),
                'start' => optional($p->fecha_entrega)->toIso8601String(),
                'url' => route('filament.admin.resources.pedidos.edit', $p),
                'color' => '#f59e0b',
                'extendedProps' => [
                    'estado' => $p->estado,
                    'cirugia' => $p->cirugia?->nombre,
                    'tipo' => 'Pedido',
                ],
            ];
        }

        usort($events, fn ($a, $b) => strcmp($a['start'] ?? '', $b['start'] ?? ''));

        $this->eventsJson = json_encode($events);
    }
}
