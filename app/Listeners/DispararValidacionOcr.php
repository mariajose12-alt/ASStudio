<?php

namespace App\Listeners;

use App\Events\ComprobanteSubido;
use App\Jobs\ProcesarComprobanteOcrJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispararValidacionOcr implements ShouldQueue
{
    public function handle(ComprobanteSubido $event): void
    {
        ProcesarComprobanteOcrJob::dispatch($event->comprobante);
    }
}
