<?php


namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NuevaReservaFotografo extends Mailable
{
    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de reserva · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reserva-nueva-fotografo',
        );
    }
}
