<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReservaModificadaCliente extends Mailable
{
    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'El fotógrafo propone un cambio en tu reserva — AS Studio',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva-modificada-cliente',
        );
    }
}
