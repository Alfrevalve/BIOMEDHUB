<?php

namespace App\Console\Commands;

use App\Mail\DigestOperativoMailable;
use App\Models\Cirugia;
use App\Models\Pedido;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarDigestOperativo extends Command
{
    protected $signature = 'digest:operativo {--to=}';
    protected $description = 'Envía el digest operativo diario (08:00 America/Lima)';

    public function handle(): int
    {
        $tz = 'America/Lima';
        $hoy = CarbonImmutable::now($tz)->startOfDay();
        $manana = $hoy->addDay();

        // Cirugías de mañana (excluye canceladas)
        $cirugias = Cirugia::query()
            ->whereDate('fecha_programada', $manana->toDateString())
            ->whereNotIn('estado', ['Cancelada'])
            ->with(['institucion']) // para mostrar nombre
            ->orderBy('fecha_programada')
            ->get();

        // Pedidos para entregar mañana que aún no estén cerrados
        $pedidos = Pedido::query()
            ->whereDate('fecha_entrega', $manana->toDateString())
            ->whereNotIn('estado', ['Entregado','Anulado','Devuelto'])
            ->orderBy('fecha_entrega')
            ->get();

        // Pedidos atrasados (pendientes) hasta hoy 08:00
        $pendientes = ['Solicitado','Preparacion','Despachado'];
        $atrasados = Pedido::query()
            ->whereIn('estado', $pendientes)
            ->whereNotNull('fecha_entrega')
            ->where('fecha_entrega', '<', CarbonImmutable::now($tz))
            ->orderBy('fecha_entrega')
            ->get();

        // Destinatarios: opción por CLI (--to=mail1,mail2) o por ENV DIGEST_TO
        $to = collect(explode(',', $this->option('to') ?: env('DIGEST_TO', '')))
                ->map(fn($e) => trim($e))
                ->filter();

        if ($to->isEmpty()) {
            // Fallback: no arruinar la ejecución si no hay destinatarios
            $this->warn('No se configuraron destinatarios (usar --to= o DIGEST_TO). Se enviará al log.');
        }

        $payload = [
            'fecha'        => $hoy->locale('es')->isoFormat('DD [de] MMMM [de] YYYY'),
            'cirugias'     => $cirugias,
            'pedidos'      => $pedidos,
            'atrasados'    => $atrasados,
            'totalCir'     => $cirugias->count(),
            'totalPed'     => $pedidos->count(),
            'totalAtr'     => $atrasados->count(),
        ];

        $subject = '🧠 Digest diario BIOMED HUB – ' . $hoy->format('d/m/Y');

        if ($to->isNotEmpty()) {
            Mail::to($to->all())->send(new DigestOperativoMailable($payload, $subject));
        } else {
            // Si no hay destinatarios, que al menos quede en el log mailer
            Mail::raw('Sin destinatarios configurados. Ver MAIL_MAILER=log y adjunta template.', function ($m) use ($subject) {
                $m->subject($subject);
            });
        }

        $this->info("Digest enviado: Cirugías={$payload['totalCir']} | Pedidos={$payload['totalPed']} | Atrasados={$payload['totalAtr']}");
        return self::SUCCESS;
    }
}
