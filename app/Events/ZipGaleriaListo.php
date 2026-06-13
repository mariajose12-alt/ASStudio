<?php

namespace App\Events;

use App\Models\Sesion;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ZipGaleriaListo
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Usuario $usuario,
        public readonly Sesion  $sesion,
        public readonly string  $urlDescarga,
        public readonly string  $tipo,
    ) {}
}
