<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;

class BienvenidaEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Usuario $usuario) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenida',
        );
    }
}
