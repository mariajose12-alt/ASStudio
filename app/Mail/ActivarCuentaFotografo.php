<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ActivarCuentaFotografo extends Mailable
{
    public function __construct(
        public Usuario $usuario,
        public string $tokenCrudo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Activa tu cuenta · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.activar-cuenta-fotografo',
            with: [
                'urlActivacion' => route('activacion.mostrar', [
                    'usuario' => $this->usuario->id,
                    'token'   => $this->tokenCrudo,
                ]),
            ],
        );
    }
}
