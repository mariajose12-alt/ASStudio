<?php

namespace App\Mail;

use App\Models\Sesion;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class GaleriaDisponibleCliente extends Mailable
{
    public function __construct(
        public Sesion $sesion,
        public string $tipo = 'seleccion' // seleccion o final
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->tipo === 'seleccion'
                ? 'Tu galería de fotos está lista · ' . config('app.name')
                : 'Tu galería final está lista · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.galeria-disponible-cliente',
        );
    }
}
