<?php

namespace App\Listeners;

use App\Events\ZipGaleriaListo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\ZipGaleriaListoCliente;

class EnviarNotificacionZipListo implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ZipGaleriaListo $event): void
    {
        $event->usuario->notify(new ZipGaleriaListoCliente(
            sesion:      $event->sesion,
            urlDescarga: $event->urlDescarga,
            tipo:        $event->tipo,
        ));
    }
}
