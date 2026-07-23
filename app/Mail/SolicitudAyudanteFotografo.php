<?php

namespace App\Mail;

use App\Models\SolicitudAyudante;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SolicitudAyudanteFotografo extends Mailable
{
    public function __construct(public SolicitudAyudante $solicitud) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de ayudante para una sesión',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-ayudante-fotografo',
        );
    }
}
