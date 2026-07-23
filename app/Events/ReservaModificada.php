<?php

namespace App\Events;

use App\Models\Reserva;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class ReservaModificada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Reserva $reserva) {}
}
