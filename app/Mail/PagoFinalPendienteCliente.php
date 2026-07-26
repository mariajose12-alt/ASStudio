<?php

namespace App\Mail;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PagoFinalPendienteCliente extends Mailable
{
    public Reserva $reserva;

    public function __construct(public Pago $pago)
    {
        $this->reserva = $pago->reserva;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu segundo pago ya está listo · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pago-final-pendiente-cliente',
            with: ['pago' => $this->pago],
        );
    }
}
