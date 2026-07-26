<?php

namespace App\Mail;

use App\Models\SolicitudEstudio;
use App\Models\Usuario;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NuevaSolicitudEstudioSocios extends Mailable
{
    public function __construct(
        public SolicitudEstudio $solicitud,
        public Usuario $socio,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva solicitud de renta de estudio · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva-solicitud-estudio-socios',
            with: ['solicitud' => $this->solicitud, 'socio' => $this->socio],
        );
    }
}
