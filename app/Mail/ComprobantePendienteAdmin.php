<?php

namespace App\Mail;

use App\Models\Pago;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ComprobantePendienteAdmin extends Mailable
{
    public function __construct(public Pago $pago) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asuntoSegunCoincidencia() . ' · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.comprobante-pendiente-admin',
        );
    }

    private function asuntoSegunCoincidencia(): string
    {
        $coincide = $this->pago->comprobante?->montoCoincideCon((float) $this->pago->monto);

        return match ($coincide) {
            true  => 'Comprobante listo para revisión (monto coincide)',
            false => 'Comprobante con discrepancia de monto',
            null  => 'Comprobante requiere revisión manual',
        };
    }
}
