<?php

namespace App\Notifications;

use App\Models\Movimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecogidaMaterialNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Movimiento $movimiento)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.movimientos.edit', $this->movimiento);

        return (new MailMessage)
            ->subject('Solicitud de recogida de material')
            ->line('Se solicitó recogida de material de cirugía.')
            ->line('Equipo: ' . ($this->movimiento->equipo?->nombre ?? ''))
            ->line('Institución: ' . ($this->movimiento->institucion?->nombre ?? ''))
            ->line('Material usado: ' . implode(', ', $this->movimiento->material_usado ?? []))
            ->action('Ver movimiento', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'recogida_material',
            'movimiento_id' => $this->movimiento->id,
            'equipo' => $this->movimiento->equipo?->nombre,
            'institucion' => $this->movimiento->institucion?->nombre,
            'material_usado' => $this->movimiento->material_usado,
            'url' => route('filament.admin.resources.movimientos.edit', $this->movimiento),
            'message' => 'Solicitud de recogida de material',
        ];
    }
}
