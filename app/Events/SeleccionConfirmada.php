<?php

namespace App\Events;

use App\Models\Sesion;
use Illuminate\Foundation\Events\Dispatchable;

class SeleccionConfirmada
{
    use Dispatchable;

    public function __construct(public Sesion $sesion) {}
}
