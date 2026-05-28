<?php

namespace App\Mail;

use App\Models\Sesion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class GaleriaDisponibleCliente extends Mailable
{
    public function __construct(public Sesion $sesion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu galería de fotos está lista — AS Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.galeria-disponible-cliente',
        );
    }
}
