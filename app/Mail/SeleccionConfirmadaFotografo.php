<?php

namespace App\Mail;

use App\Models\Sesion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SeleccionConfirmadaFotografo extends Mailable
{
    public function __construct(public Sesion $sesion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'El cliente confirmó su selección de fotos — AS Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seleccion-confirmada-fotografo',
        );
    }
}
