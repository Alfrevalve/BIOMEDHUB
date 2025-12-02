<?php

namespace App\Filament\Widgets;

use App\Models\Cirugia;
use App\Models\Equipo;
use App\Models\Movimiento;
use App\Models\Pedido;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();

        $cirugiasProximas = Cirugia::query()
            ->whereBetween('fecha_programada', [$now, $now->copy()->addDays(7)])
            ->where('estado', '!=', 'Cancelada')
            ->count();

        $pedidosPendientes = Pedido::query()
            ->whereNotIn('estado', ['Entregado', 'Anulado'])
            ->count();

        $pedidosEntregaHoy = Pedido::query()
            ->whereDate('fecha_entrega', $now->toDateString())
            ->whereNotIn('estado', ['Entregado', 'Anulado'])
            ->count();

        $movimientosActivos = Movimiento::query()
            ->whereIn('estado_mov', ['Programado', 'En uso'])
            ->count();

        $equiposDisponibles = Equipo::query()
            ->where('estado_actual', 'Disponible')
            ->count();

        $cirugiasHoy = Cirugia::query()
            ->whereDate('fecha_programada', $now->toDateString())
            ->where('estado', '!=', 'Cancelada')
            ->count();

        return [
            Stat::make('Cirugías próximas (7d)', $cirugiasProximas)
                ->description('Agenda inmediata')
                ->color('primary')
                ->icon('heroicon-o-heart')
                ->extraAttributes(['class' => 'tile-primary']),
            Stat::make('Cirugías hoy', $cirugiasHoy)
                ->description('Programadas para hoy')
                ->color('info')
                ->icon('heroicon-o-heart')
                ->extraAttributes(['class' => 'tile-deep']),
            Stat::make('Pedidos pendientes', $pedidosPendientes)
                ->description('Logística')
                ->color('info')
                ->icon('heroicon-o-inbox-stack')
                ->extraAttributes(['class' => 'tile-surface']),
            Stat::make('Pedidos entrega hoy', $pedidosEntregaHoy)
                ->description('Entregas del día')
                ->color('success')
                ->icon('heroicon-o-inbox')
                ->extraAttributes(['class' => 'tile-accent']),
            Stat::make('Movimientos activos', $movimientosActivos)
                ->description('Traslados en curso')
                ->color('success')
                ->icon('heroicon-o-truck')
                ->extraAttributes(['class' => 'tile-green']),
            Stat::make('Equipos disponibles', $equiposDisponibles)
                ->description('Listos para uso')
                ->color('success')
                ->icon('heroicon-o-wrench-screwdriver')
                ->extraAttributes(['class' => 'tile-muted']),
        ];
    }
}
