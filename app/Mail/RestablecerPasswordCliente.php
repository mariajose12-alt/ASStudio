<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RestablecerPasswordCliente extends Mailable
{
    public function __construct(
        public Usuario $usuario,
        public string $tokenCrudo,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablece tu contraseña · ' . config('app.name')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.restablecer-password-cliente',
            with: [
                'urlActivacion' => route('activacion.mostrar', [
                    'usuario' => $this->usuario->id,
                    'token'   => $this->tokenCrudo,
                ]),
            ],
        );
    }
}
