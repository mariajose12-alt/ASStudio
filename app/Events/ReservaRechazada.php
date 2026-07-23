<?php

namespace App\Events;

use App\Models\Reserva;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class ReservaRechazada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Reserva $reserva) {}
}
