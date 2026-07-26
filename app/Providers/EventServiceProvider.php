<?php

namespace App\Providers;

use App\Events\ComprobanteSubido;
use App\Events\GaleriaDisponible;
use App\Events\PagoConfirmado;
use App\Events\PagoRechazado;
use App\Events\ReservaAprobada;
use App\Events\ReservaCreada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Events\SeleccionConfirmada;
use App\Events\SolicitudEstudioCreada;
use App\Listeners\NotificarSociosNuevaSolicitud;
use App\Events\ZipGaleriaListo;
use App\Listeners\CrearPagoFinalAlConfirmarSeleccion;
use App\Listeners\DispararValidacionOcr;
use App\Listeners\EnviarNotificacionGaleriaLista;
use App\Listeners\EnviarNotificacionNuevaReserva;
use App\Listeners\EnviarNotificacionPagoConfirmado;
use App\Listeners\EnviarNotificacionPagoRechazado;
use App\Listeners\EnviarNotificacionReservaAprobada;
use App\Listeners\EnviarNotificacionReservaModificada;
use App\Listeners\EnviarNotificacionReservaRechazada;
use App\Listeners\EnviarNotificacionSeleccion;
use App\Listeners\EnviarNotificacionZipListo;
use App\Events\SolicitudAyudanteCreada;
use App\Listeners\EnviarNotificacionSolicitudAyudante;
use App\Listeners\AvanzarReservaPorPagoConfirmado;
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
            AvanzarReservaPorPagoConfirmado::class,
        ],
        PagoRechazado::class => [
            EnviarNotificacionPagoRechazado::class
        ],
        ComprobanteSubido::class => [
            DispararValidacionOcr::class
        ],
        GaleriaDisponible::class => [
            EnviarNotificacionGaleriaLista::class,
        ],
        SeleccionConfirmada::class => [
            EnviarNotificacionSeleccion::class,
            CrearPagoFinalAlConfirmarSeleccion::class,
        ],
        ZipGaleriaListo::class => [
            EnviarNotificacionZipListo::class,
        ],
        SolicitudAyudanteCreada::class => [
            EnviarNotificacionSolicitudAyudante::class,
        ],
        SolicitudEstudioCreada::class => [
            NotificarSociosNuevaSolicitud::class,
        ]
    ];
}
