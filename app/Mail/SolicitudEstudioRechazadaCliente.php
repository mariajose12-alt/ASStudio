<?php

namespace App\Mail;

use App\Models\SolicitudEstudio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SolicitudEstudioRechazadaCliente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SolicitudEstudio $solicitud) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Tu reserva fue rechazada! · Zehcnas STUDIO'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitud-estudio-rechazada-cliente',
            with: ['solicitud' => $this->solicitud], // por si la vista no recibe la propiedad pública automáticamente
        );
    }
}
