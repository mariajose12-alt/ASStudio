<?php

namespace App\Events;

use App\Models\Pago;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PagoRechazado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Pago $pago,
        public ?int $comprobanteAnteriorId = null
    ) {
    }
}
