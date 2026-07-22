<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecordatorioSesionCliente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function build()
    {
        return $this->subject('Recordatorio: tu sesión es mañana')
            ->view('emails.recordatorio-sesion-cliente')
            ->with([
                'reserva' => $this->reserva,
                'cliente' => $this->reserva->cliente,
                'fecha'   => $this->reserva->fecha_inicio->format('d/m/Y'),
                'hora'    => $this->reserva->fecha_inicio->format('H:i'),
            ]);
    }
}
