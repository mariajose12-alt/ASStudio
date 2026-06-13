<?php

namespace App\Listeners;

use App\Events\ZipGaleriaListo;
use App\Mail\ZipGaleriaListoCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionZipListo implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ZipGaleriaListo $event): void
    {
        Mail::to($event->usuario->email)
            ->send(new ZipGaleriaListoCliente(
                sesion:      $event->sesion,
                urlDescarga: $event->urlDescarga,
                tipo:        $event->tipo,
            ));
    }
}
