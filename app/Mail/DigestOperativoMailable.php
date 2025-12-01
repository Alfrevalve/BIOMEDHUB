<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DigestOperativoMailable extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;
    public string $subjectLine;

    public function __construct(array $data, string $subjectLine)
    {
        $this->data = $data;
        $this->subjectLine = $subjectLine;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.digest_operativo')
            ->with($this->data);
    }
}
