<?php

namespace App\Events;

use App\Models\Comprobante;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ComprobanteSubido
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Comprobante $comprobante)
    {
    }
}
