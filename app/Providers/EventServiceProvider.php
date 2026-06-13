<?php

namespace App\Providers;

use App\Events\GaleriaDisponible;
use App\Events\PagoConfirmado;
use App\Events\ReservaAprobada;
use App\Events\ReservaCreada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Events\SeleccionConfirmada;
use App\Listeners\EnviarNotificacionGaleriaLista;
use App\Listeners\EnviarNotificacionNuevaReserva;
use App\Listeners\EnviarNotificacionPagoConfirmado;
use App\Listeners\EnviarNotificacionReservaAprobada;
use App\Listeners\EnviarNotificacionReservaModificada;
use App\Listeners\EnviarNotificacionReservaRechazada;
use App\Listeners\EnviarNotificacionSeleccion;
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
        PagoConfirmado::class => [
            EnviarNotificacionPagoConfirmado::class,
        ],
        GaleriaDisponible::class => [
            EnviarNotificacionGaleriaLista::class,
        ],
        SeleccionConfirmada::class => [
            EnviarNotificacionSeleccion::class,
        ],
        ZipGaleriaListo::class => [
            EnviarNotificacionZipListo::class,
        ],
    ];
}
