<?php

namespace App\Mail;

use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PagoRechazadoCliente extends Mailable
{
    public Reserva $reserva;
    public function __construct(public Pago $pago)
    {
        $this->reserva = $pago->reserva;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Necesitamos que revises tu comprobante · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pago-rechazado-cliente',
            with: ['pago' => $this->pago],
        );
    }
}
