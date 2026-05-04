<?php

namespace App\Events;

use App\Models\Reserva;
use Illuminate\Foundation\Events\Dispatchable;

class ReservaModificada
{
    use Dispatchable;
    public function __construct(public Reserva $reserva) {}
}
