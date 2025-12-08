<?php

namespace App\Notifications;

use App\Models\Cirugia;
use App\Models\CirugiaReporte;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CirugiaConsumoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Cirugia $cirugia, protected CirugiaReporte $reporte)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = [];
        if (method_exists($notifiable, 'prefersInApp') ? $notifiable->prefersInApp() : true) {
            $channels[] = 'database';
        }
        if (method_exists($notifiable, 'prefersMail') ? $notifiable->prefersMail() : true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.cirugias.edit', $this->cirugia);
        $fecha = optional($this->cirugia->fecha_programada)->timezone('America/Lima')->format('d/m/Y H:i');

        return (new MailMessage)
            ->subject('Consumo registrado - ' . ($this->cirugia->nombre ?? 'Cirugía'))
            ->line('Se registró el consumo y evidencia de una cirugía.')
            ->line('Cirugía: ' . ($this->cirugia->nombre ?? ''))
            ->line('Fecha programada: ' . $fecha)
            ->line('Institución: ' . ($this->cirugia->institucion?->nombre ?? ''))
            ->line('Paciente: ' . ($this->reporte->paciente ?? ''))
            ->line('Consumo: ' . ($this->reporte->consumo ?: 'No detallado'))
            ->action('Revisar reporte', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'cirugia_consumo',
            'cirugia_id' => $this->cirugia->id,
            'reporte_id' => $this->reporte->id,
            'cirugia' => $this->cirugia->nombre,
            'institucion' => $this->cirugia->institucion?->nombre,
            'paciente' => $this->reporte->paciente,
            'consumo' => $this->reporte->consumo,
            'url' => route('filament.admin.resources.cirugias.edit', $this->cirugia),
            'message' => 'Consumo registrado para la cirugía ' . ($this->cirugia->nombre ?? ''),
        ];
    }
}
