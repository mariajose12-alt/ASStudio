<?php

namespace App\Events;

use App\Models\Sesion;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class GaleriaDisponible
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Sesion $sesion) {}
}
