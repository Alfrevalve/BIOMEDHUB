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
            ->with(['institucion'])
            ->orderBy('fecha_programada')
            ->get();

        // Pedidos para entregar mañana que aún no están cerrados
        $pedidos = Pedido::query()
            ->whereDate('fecha_entrega', $manana->toDateString())
            ->whereNotIn('estado', ['Entregado', 'Anulado', 'Devuelto'])
            ->orderBy('fecha_entrega')
            ->get();

        // Pedidos atrasados (pendientes) hasta ahora
        $pendientes = ['Solicitado', 'Preparacion', 'Despachado'];
        $atrasados = Pedido::query()
            ->whereIn('estado', $pendientes)
            ->whereNotNull('fecha_entrega')
            ->where('fecha_entrega', '<', CarbonImmutable::now($tz))
            ->orderBy('fecha_entrega')
            ->get();

        // Destinatarios: opción por CLI (--to=mail1,mail2) o por ENV DIGEST_TO
        $to = collect(explode(',', $this->option('to') ?: env('DIGEST_TO', '')))
            ->map(fn ($e) => trim($e))
            ->filter();

        $payload = [
            'fecha'     => $hoy->locale('es')->isoFormat('DD [de] MMMM [de] YYYY'),
            'cirugias'  => $cirugias,
            'pedidos'   => $pedidos,
            'atrasados' => $atrasados,
            'totalCir'  => $cirugias->count(),
            'totalPed'  => $pedidos->count(),
            'totalAtr'  => $atrasados->count(),
        ];

        $subject = 'Digest diario BIOMED HUB - ' . $hoy->format('d/m/Y');

        if ($to->isEmpty()) {
            $this->warn('Sin destinatarios (--to= o DIGEST_TO). Se envía al mailer de log para no fallar el cron.');
            $mailer = config('mail.fallback_mailer') ?? 'log';
            Mail::mailer($mailer)
                ->to('digest-log@localhost')
                ->queue(new DigestOperativoMailable($payload, $subject));
        } else {
            Mail::to($to->all())->queue(new DigestOperativoMailable($payload, $subject));
        }

        // Resumen opcional para facturación
        $toFact = collect(explode(',', env('DIGEST_FACT_TO', '')))
            ->map(fn ($e) => trim($e))
            ->filter();
        if ($toFact->isNotEmpty()) {
            $subjectFact = 'Resumen facturación - ' . $hoy->format('d/m/Y');
            Mail::to($toFact->all())->queue(new DigestOperativoMailable($payload, $subjectFact));
        }

        $this->info("Digest enviado: Cirugías={$payload['totalCir']} | Pedidos={$payload['totalPed']} | Atrasados={$payload['totalAtr']}");
        return self::SUCCESS;
    }
}
