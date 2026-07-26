<?php

namespace App\Mail;

use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ZipGaleriaListoCliente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Sesion $sesion,
        public readonly string $urlDescarga,
        public readonly string $tipo,        // 'originales' | 'editadas'
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->tipo === 'editadas' ? 'editadas' : 'originales';

        return new Envelope(
            subject: "Tus fotos {$label} están listas para descargar — Abraham Sánchez",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.zip-listo-cliente',
        );
    }
}
