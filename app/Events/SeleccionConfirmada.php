<?php

namespace App\Events;

use App\Models\Sesion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeleccionConfirmada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Sesion $sesion) {}
}
