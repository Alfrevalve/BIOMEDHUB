<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Cirugias\CirugiaResource;
use App\Filament\Resources\Equipos\EquipoResource;
use App\Filament\Resources\Movimientos\MovimientoResource;
use App\Filament\Resources\Pedidos\PedidoResource;
use App\Models\Cirugia;
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

        $stockCritico = Item::query()
            ->whereRaw("{$stockExpr} <= 5")
            ->count();

        return [
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
                ->description('Items con <= 5 unid.')
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
