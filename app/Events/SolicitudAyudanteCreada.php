<?php

namespace App\Events;

use App\Models\SolicitudAyudante;
use Illuminate\Foundation\Events\Dispatchable;

class SolicitudAyudanteCreada
{
    use Dispatchable;
    public function __construct(public SolicitudAyudante $solicitud) {}
}
