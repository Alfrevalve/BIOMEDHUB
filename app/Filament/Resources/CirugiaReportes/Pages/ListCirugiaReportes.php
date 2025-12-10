<?php

namespace App\Filament\Resources\CirugiaReportes\Pages;

use App\Filament\Resources\CirugiaReportes\CirugiaReporteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCirugiaReportes extends ListRecords
{
    protected static string $resource = CirugiaReporteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('crear')
                ->label('Crear')
                ->color('primary')
                ->icon('heroicon-o-plus')
                ->url(fn () => static::getResource()::getUrl('create'))
                ->visible(fn () => static::getResource()::canCreate()),
            Actions\Action::make('export_csv')
                ->label('Exportar CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $filename = 'cirugia_reportes_' . now('America/Lima')->format('Ymd_His') . '.csv';
                    $reports = $this->getTableQuery()->with(['cirugia', 'cirugia.pedidos'])->get();

                    $csv = implode(',', [
                        'cirugia',
                        'institucion',
                        'paciente',
                        'hora_programada',
                        'hora_inicio',
                        'hora_termino',
                        'consumo',
                        'estado_pedido',
                        'fecha_reporte',
                    ]) . "\n";

                    foreach ($reports as $reporte) {
                        $estadoPedido = optional($reporte->cirugia?->pedidos()->latest('id')->first())->estado;
                        $csv .= implode(',', [
                            '"' . str_replace('"', '""', $reporte->cirugia?->nombre ?? '') . '"',
                            '"' . str_replace('"', '""', $reporte->institucion ?? '') . '"',
                            '"' . str_replace('"', '""', $reporte->paciente ?? '') . '"',
                            optional($reporte->hora_programada)->timezone('America/Lima')->format('Y-m-d H:i'),
                            optional($reporte->hora_inicio)->timezone('America/Lima')->format('Y-m-d H:i'),
                            optional($reporte->hora_termino)->timezone('America/Lima')->format('Y-m-d H:i'),
                            '"' . str_replace('"', '""', $reporte->consumo ?? '') . '"',
                            '"' . str_replace('"', '""', $estadoPedido ?? '') . '"',
                            optional($reporte->created_at)->timezone('America/Lima')->format('Y-m-d H:i'),
                        ]) . "\n";
                    }

                    return response($csv, 200, [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    ]);
                }),
        ];
    }
}
