<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Message $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('filament.admin.pages.mensajes');
        $snippet = str()->limit(strip_tags((string) $this->message->body), 120);

        return (new MailMessage)
            ->subject('[BiomedHub] Nuevo mensaje: ' . ($this->message->subject ?? ''))
            ->line('Has recibido un nuevo mensaje interno.')
            ->line('De: ' . ($this->message->sender?->name ?? 'Usuario'))
            ->line('Asunto: ' . ($this->message->subject ?? 'Sin asunto'))
            ->line($snippet)
            ->action('Abrir bandeja', $url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'mensaje_interno',
            'message_id' => $this->message->id,
            'subject' => $this->message->subject,
            'sender' => $this->message->sender?->name,
            'snippet' => str()->limit(strip_tags((string) $this->message->body), 120),
            'url' => route('filament.admin.pages.mensajes'),
        ];
    }
}
