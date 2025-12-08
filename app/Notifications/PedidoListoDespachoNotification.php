<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoListoDespachoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Pedido $pedido)
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
        $url = route('filament.admin.resources.pedidos.edit', $this->pedido);
        $fechaEntrega = optional($this->pedido->fecha_entrega)->timezone('America/Lima')->format('d/m/Y H:i');

        return (new MailMessage)
            ->subject("Pedido listo para despacho - {$this->pedido->codigo_pedido}")
            ->line('El pedido quedó listo para despacho.')
            ->line('Pedido: ' . $this->pedido->codigo_pedido)
            ->line('Cirugía: ' . ($this->pedido->cirugia?->nombre ?? ''))
            ->line('Fecha entrega: ' . ($fechaEntrega ?: 'No definida'))
            ->line('Transportista: ' . ($this->pedido->transportista ?: 'Pendiente'))
            ->action('Abrir pedido', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'pedido_listo_despacho',
            'pedido_id' => $this->pedido->id,
            'codigo_pedido' => $this->pedido->codigo_pedido,
            'cirugia' => $this->pedido->cirugia?->nombre,
            'transportista' => $this->pedido->transportista,
            'fecha_entrega' => $this->pedido->fecha_entrega,
            'url' => route('filament.admin.resources.pedidos.edit', $this->pedido),
            'message' => "Pedido {$this->pedido->codigo_pedido} listo para despacho",
        ];
    }
}
