<?php

namespace App\Events;

use App\Models\SolicitudEstudio;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolicitudEstudioCreada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SolicitudEstudio $solicitud) {}
}
