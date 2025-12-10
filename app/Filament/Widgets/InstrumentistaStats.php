<?php

namespace App\Filament\Widgets;

use App\Models\Cirugia;
use App\Models\CirugiaReporte;
use App\Models\Pedido;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InstrumentistaStats extends BaseWidget
{
    public string $period = 'week';

    public function content(Schema $schema): Schema
    {
        $buttons = collect([
            'week' => 'Semana',
            'month' => 'Mes',
            'year' => 'Año',
        ])->map(fn ($label, $key) => sprintf(
            '<button type="button" wire:click.prevent="setPeriod(\'%s\')" class="bh-period-btn %s">%s</button>',
            $key,
            $this->period === $key ? 'is-active' : '',
            $label
        ))->implode('');

        return $schema->components([
            Html::make('<div class="bh-period-switch"><span class="bh-period-label">Periodo</span><div class="bh-period-buttons">'.$buttons.'</div></div>'),
            $this->getSectionContentComponent(),
        ]);
    }

    public function setPeriod(string $period): void
    {
        $this->period = $period;
        $this->cachedStats = null;
    }

    private function getRange(): array
    {
        $now = now();
        $period = $this->period ?? 'week';

        return match ($period) {
            'month' => [$now->clone()->startOfMonth(), $now->clone()->endOfMonth()],
            'year' => [$now->clone()->startOfYear(), $now->clone()->endOfYear()],
            default => [$now->clone()->startOfWeek(), $now->clone()->endOfWeek()],
        };
    }

    private function rangeLabel(): string
    {
        return match ($this->period ?? 'week') {
            'month' => 'este mes',
            'year' => 'este Año',
            default => 'esta semana',
        };
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $roles = $user->getRoleNames()->map(fn (string $role) => strtolower($role));
        $isAdminLike = $roles->contains(fn (string $r) => in_array($r, ['admin', 'administrador', 'administrator'], true));
        $isInstrumentista = $roles->contains('instrumentista');

        if (! $isAdminLike && ! $isInstrumentista) {
            return [];
        }

        $filterByInstrumentista = $isInstrumentista && ! $isAdminLike;
        [$from, $to] = $this->getRange();
        $label = $this->rangeLabel();

        $cirugiasHoy = Cirugia::query()
            ->when($filterByInstrumentista, fn ($q) => $q->where('instrumentista_id', $user->id))
            ->whereBetween('fecha_programada', [$from, $to])
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $cirugiasProximas = Cirugia::query()
            ->when($filterByInstrumentista, fn ($q) => $q->where('instrumentista_id', $user->id))
            ->whereBetween('fecha_programada', [$from, $to])
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $cirugiasSinReporte = Cirugia::query()
            ->when($filterByInstrumentista, fn ($q) => $q->where('instrumentista_id', $user->id))
            ->whereBetween('fecha_programada', [$from, $to])
            ->where('estado', '!=', 'Cancelada')
            ->whereDoesntHave('reportes')
            ->count();

        $pedidosPendientes = Pedido::query()
            ->when(
                $filterByInstrumentista,
                fn ($q) => $q->whereHas('cirugia', fn ($cq) => $cq->where('instrumentista_id', $user->id))
            )
            ->whereNotIn('estado', ['Entregado', 'Anulado', 'Devuelto'])
            ->count();

        $reportesUltimos7 = CirugiaReporte::query()
            ->when(
                $filterByInstrumentista,
                fn ($q) => $q->whereHas('cirugia', fn ($cq) => $cq->where('instrumentista_id', $user->id))
            )
            ->whereBetween('created_at', [$from, $to])
            ->count();

        return [
            Stat::make('Cirugias', $cirugiasHoy)
                ->description("Asignadas en {$label}")
                ->icon('heroicon-o-heart')
                ->url(\App\Filament\Resources\Cirugias\CirugiaResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#0ea5e9,#2563eb);color:#fff']),
            Stat::make('Agenda', $cirugiasProximas)
                ->description("Programadas en {$label}")
                ->icon('heroicon-o-calendar')
                ->url(\App\Filament\Pages\Calendario::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#fff']),
            Stat::make('Sin reporte', $cirugiasSinReporte)
                ->description('Cirugias sin reporte')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(\App\Filament\Resources\CirugiaReportes\CirugiaReporteResource::getUrl())
                ->extraAttributes(['style' => $cirugiasSinReporte > 0
                    ? 'background:linear-gradient(135deg,#ef4444,#b91c1c);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Pedidos pendientes', $pedidosPendientes)
                ->description('Pedidos de mis cirugias')
                ->icon('heroicon-o-inbox-stack')
                ->url(\App\Filament\Resources\Pedidos\PedidoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff']),
            Stat::make('Reportes 7d', $reportesUltimos7)
                ->description('Registrados ultimos 7 dias')
                ->icon('heroicon-o-document-text')
                ->url(\App\Filament\Resources\CirugiaReportes\CirugiaReporteResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}
