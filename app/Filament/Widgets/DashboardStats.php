<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Filament\Resources\Equipos\EquipoResource;
use App\Filament\Resources\Movimientos\MovimientoResource;
use App\Filament\Resources\Movimientos\Pages\RecojosPendientes;
use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\Cirugia;
use App\Models\CirugiaReporte;
use App\Models\Equipo;
use App\Models\Item;
use App\Models\Movimiento;
use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();

        $cirugiasHoy = Cirugia::query()
            ->whereDate('fecha_programada', $now->toDateString())
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $cirugiasProximas = Cirugia::query()
            ->whereBetween('fecha_programada', [$now, $now->copy()->addDays(7)])
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $pedidosPendientes = Pedido::query()
            ->whereNotIn('estado', ['Entregado', 'Anulado'])
            ->count();

        $pedidosPreparacion = Pedido::query()
            ->where('estado', 'Preparacion')
            ->count();

        $pedidosEntregaHoy = Pedido::query()
            ->whereDate('fecha_entrega', $now->toDateString())
            ->whereNotIn('estado', ['Entregado', 'Anulado'])
            ->count();

        $pedidosAtrasados = Pedido::query()
            ->whereNotIn('estado', ['Entregado', 'Anulado', 'Devuelto'])
            ->whereNotNull('fecha_entrega')
            ->where('fecha_entrega', '<', $now)
            ->count();

        $movimientosActivos = Movimiento::query()
            ->whereIn('estado_mov', ['Programado', 'En uso'])
            ->count();

        $retornosPendientes = Movimiento::query()
            ->where('estado_mov', 'En uso')
            ->count();

        $equiposDisponibles = Equipo::query()
            ->where('estado_actual', 'Disponible')
            ->count();

        $stockExpr = 'CAST(COALESCE(stock_total,0) AS SIGNED) - CAST(COALESCE(stock_reservado,0) AS SIGNED)';
        $critico = (int) config('biomedhub.stock_critico_threshold', 5);

        $stockCritico = Item::query()
            ->whereRaw("{$stockExpr} <= ?", [$critico])
            ->count();

        $listosDespacho = Pedido::query()
            ->whereNotNull('listo_despacho_at')
            ->whereIn('estado', ['Solicitado', 'Preparacion', 'Despachado'])
            ->count();

        $recojosSolicitados = Movimiento::query()
            ->whereNotNull('recogida_solicitada_at')
            ->whereIn('estado_mov', ['Devuelto', 'En uso', 'Programado'])
            ->count();

        $consumosSinFacturar = CirugiaReporte::query()
            ->whereHas('cirugia.pedidos', fn ($q) => $q->whereNotIn('estado', ['Devuelto', 'Anulado']))
            ->count();

        return [
            Stat::make('Listos para despacho', $listosDespacho)
                ->description('Pedidos marcados listos')
                ->icon('heroicon-o-bell-alert')
                ->url(PedidoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff']),
            Stat::make('Recojos solicitados', $recojosSolicitados)
                ->description('Pendientes de recoger')
                ->icon('heroicon-o-truck')
                ->url(RecojosPendientes::getUrl())
                ->extraAttributes(['style' => $recojosSolicitados > 0
                    ? 'background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Consumidos sin facturar', $consumosSinFacturar)
                ->description('Reportes con pedido activo')
                ->icon('heroicon-o-document-check')
                ->url(\App\Filament\Resources\CirugiaReportes\CirugiaReporteResource::getUrl())
                ->extraAttributes(['style' => $consumosSinFacturar > 0
                    ? 'background:linear-gradient(135deg,#e11d48,#be123c);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Cirugias hoy', $cirugiasHoy)
                ->description('Programadas hoy')
                ->icon('heroicon-o-heart')
                ->url(CirugiaResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#0ea5e9,#2563eb);color:#fff']),
            Stat::make('Cirugias proximas (7d)', $cirugiasProximas)
                ->description('Agenda inmediata')
                ->icon('heroicon-o-heart')
                ->url(CirugiaResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#0ea5e9,#14b8a6);color:#fff']),
            Stat::make('Pedidos pendientes', $pedidosPendientes)
                ->description('Logistica abierta')
                ->icon('heroicon-o-inbox-stack')
                ->url(PedidoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#0ea5e9,#0ea5e9,#0f172a);color:#fff']),
            Stat::make('En preparacion', $pedidosPreparacion)
                ->description('Pedidos en alistado')
                ->icon('heroicon-o-wrench-screwdriver')
                ->url(PedidoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff']),
            Stat::make('Entrega hoy', $pedidosEntregaHoy)
                ->description('Planificados hoy')
                ->icon('heroicon-o-inbox')
                ->url(PedidoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Pedidos atrasados', $pedidosAtrasados)
                ->description('Requieren accion')
                ->icon('heroicon-o-clock')
                ->url(PedidoResource::getUrl())
                ->extraAttributes(['style' => $pedidosAtrasados > 0
                    ? 'background:linear-gradient(135deg,#ef4444,#b91c1c);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Movimientos activos', $movimientosActivos)
                ->description('Programado / En uso')
                ->icon('heroicon-o-truck')
                ->url(MovimientoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#10b981,#059669);color:#fff']),
            Stat::make('Retornos pendientes', $retornosPendientes)
                ->description('En uso sin retorno')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->url(MovimientoResource::getUrl())
                ->extraAttributes(['style' => $retornosPendientes > 0
                    ? 'background:linear-gradient(135deg,#f59e0b,#f97316);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
            Stat::make('Equipos disponibles', $equiposDisponibles)
                ->description('Listos para uso')
                ->icon('heroicon-o-wrench-screwdriver')
                ->url(EquipoResource::getUrl())
                ->extraAttributes(['style' => 'background:linear-gradient(135deg,#14b8a6,#0ea5e9);color:#fff']),
            Stat::make('Stock critico', $stockCritico)
                ->description("Items con <= {$critico} unid.")
                ->icon('heroicon-o-exclamation-triangle')
                ->extraAttributes(['style' => $stockCritico > 0
                    ? 'background:linear-gradient(135deg,#ef4444,#b91c1c);color:#fff'
                    : 'background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff']),
        ];
    }

    protected function getColumns(): int|array
    {
        // 5 tarjetas por fila en pantallas grandes, menos en móviles.
        return [
            'default' => 1,
            'md' => 3,
            'xl' => 5,
        ];
    }
}
