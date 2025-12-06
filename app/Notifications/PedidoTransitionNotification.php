<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoTransitionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Pedido $pedido, protected string $etapa)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.resources.pedidos.edit', $this->pedido);

        return (new MailMessage)
            ->subject("Pedido {$this->pedido->codigo_pedido} - {$this->etapa}")
            ->line("El pedido {$this->pedido->codigo_pedido} ha pasado a {$this->etapa}")
            ->line('Cirugía: ' . ($this->pedido->cirugia?->nombre ?? ''))
            ->line('Entrega: ' . optional($this->pedido->fecha_entrega)->timezone('America/Lima')->format('d/m/Y H:i'))
            ->action('Abrir en panel', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'pedido_transicion',
            'pedido_id' => $this->pedido->id,
            'codigo_pedido' => $this->pedido->codigo_pedido,
            'estado' => $this->pedido->estado,
            'cirugia' => $this->pedido->cirugia?->nombre,
            'etapa' => $this->etapa,
            'url' => route('filament.admin.resources.pedidos.edit', $this->pedido),
            'message' => "Pedido {$this->pedido->codigo_pedido} ha pasado a {$this->etapa}",
        ];
    }
}
