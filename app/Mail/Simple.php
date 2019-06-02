<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Simple extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct($sender, $subject, $body)
    {
        $this->from($sender)
            ->subject($subject)
            ->text('emails.simple', ['body' => $body]);
    }

    public function build()
    {
        return $this;
    }
}
