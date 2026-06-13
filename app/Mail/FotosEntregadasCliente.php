<?php

namespace App\Mail;

use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FotosEntregadasCliente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Sesion $sesion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tus fotos editadas están listas! · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
        // Crea la vista: resources/views/emails/fotos-entregadas-cliente.blade.php
            view: 'emails.fotos-entregadas-cliente',
        );
    }
}
