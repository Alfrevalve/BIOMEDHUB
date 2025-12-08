<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MessageFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_crear_mensaje_envia_notificacion_y_guarda_en_bd(): void
    {
        Notification::fake();

        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $message = Message::create([
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Asunto de prueba',
            'body' => '<p>Contenido interno</p>',
        ]);

        Notification::send($recipient, new MessageReceivedNotification($message));

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'sender_id' => $sender->id,
            'recipient_id' => $recipient->id,
            'subject' => 'Asunto de prueba',
        ]);

        Notification::assertSentTo(
            $recipient,
            MessageReceivedNotification::class,
            function (MessageReceivedNotification $notification) use ($message) {
                return $notification->toDatabase($message->recipient)['message_id'] === $message->id;
            }
        );
    }

    public function test_mark_as_read_actualiza_fecha(): void
    {
        $message = Message::factory()->create(['read_at' => null]);

        $this->assertNull($message->read_at);

        $message->markAsRead();
        $message->refresh();

        $this->assertNotNull($message->read_at);
    }
}
