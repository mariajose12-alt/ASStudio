<?php

namespace App\Mail;

use App\Models\PostulacionAyudante;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PostulacionConfirmadaFotografo extends Mailable
{
    public function __construct(public PostulacionAyudante $postulacion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Fuiste confirmado como ayudante! '
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.postulacion-confirmada-fotografo',
        );
    }
}
