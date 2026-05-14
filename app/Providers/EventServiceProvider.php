<?php

namespace App\Providers;

use App\Events\ReservaAprobada;
use App\Events\ReservaCreada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Listeners\EnviarNotificacionNuevaReserva;
use App\Listeners\EnviarNotificacionReservaAprobada;
use App\Listeners\EnviarNotificacionReservaModificada;
use App\Listeners\EnviarNotificacionReservaRechazada;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ReservaCreada::class => [
            EnviarNotificacionNuevaReserva::class,
        ],
        ReservaAprobada::class => [
            EnviarNotificacionReservaAprobada::class,
        ],
        ReservaRechazada::class => [
            EnviarNotificacionReservaRechazada::class,
        ],
        ReservaModificada::class => [
            EnviarNotificacionReservaModificada::class,
        ],

//        PagoConfirmado::class => [
//            EnviarNotificacionPagoConfirmado::class,   // al cliente
//        ],
//
//        GaleriaDisponible::class => [
//            EnviarNotificacionGaleriaLista::class,     // al cliente
//        ],
    ];
}
