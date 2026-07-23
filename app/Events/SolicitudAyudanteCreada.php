<?php

namespace App\Events;

use App\Models\SolicitudAyudante;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class SolicitudAyudanteCreada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public SolicitudAyudante $solicitud) {}
}
