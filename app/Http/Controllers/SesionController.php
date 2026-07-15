<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sesion;
use Illuminate\Support\Facades\Auth;
use App\Models\SolicitudAyudante;
use App\Services\SolicitudAyudanteService;

class SesionController extends Controller
{
    protected SolicitudAyudanteService $ayudanteService;

    public function __construct(SolicitudAyudanteService $ayudanteService)
    {
        $this->ayudanteService = $ayudanteService;
    }
    public function index()
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $sesiones = Sesion::with([
            'reserva.cliente.usuario.persona',
            'reserva.paquete',
            'fotografias',
            'solicitudesAyudante.postulaciones.fotografo.empleado.usuario.persona',
        ])
            ->where(function ($query) use ($fotografo) {

                // Soy fotógrafo principal
                $query->whereHas('reserva', function ($q) use ($fotografo) {
                    $q->where('fotografo_id', $fotografo->id);
                })

                    // O soy asistente
                    ->orWhereHas('participaciones', function ($q) use ($fotografo) {
                        $q->where('fotografo_id', $fotografo->id)
                            ->where('estado_participacion', true);
                    });

            })
            ->whereIn('estado', ['CONFIRMADA', 'EN_PROCESO', 'EN_EDICION'])
            ->orderByDesc('fecha_inicio')
            ->get();

        $confirmadas = $sesiones->where('estado', 'CONFIRMADA')->values();
        $enProceso = $sesiones->where('estado', 'EN_PROCESO')->values();
        $enEdicion = $sesiones->where('estado', 'EN_EDICION')->values();

        // Solicitudes propias abiertas (para confirmar postulantes)
        $solicitudesPropias = SolicitudAyudante::whereHas('sesion.reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->where('estado', 'ABIERTA')
            ->with(['sesion.reserva', 'postulaciones.fotografo.empleado.usuario.persona'])
            ->get();

        // Solicitudes de otros fotógrafos donde este fotógrafo está disponible y no se ha postulado aún
        $solicitudesDisponibles = SolicitudAyudante::where('fotografo_solicitante_id', '!=', $fotografo->id)
            ->where('estado', 'ABIERTA')
            ->whereDoesntHave('postulaciones', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->with(['sesion.reserva', 'solicitante.empleado.usuario.persona'])
            ->get()
            ->filter(fn($solicitud) => $this->ayudanteService->estaDisponible($solicitud->sesion, $fotografo))
            ->values();


        return view('fotografo.sesiones', compact(
            'confirmadas',
            'enProceso',
            'enEdicion',
            'solicitudesPropias',
            'solicitudesDisponibles',
            'fotografo'
        ));
    }

    // Botón provisional para pasar de CONFIRMADA a EN_PROCESO
    public function iniciar(int $id)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $sesion = Sesion::whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->where('estado', 'CONFIRMADA')
            ->findOrFail($id);

        $sesion->update(['estado' => 'EN_PROCESO']);

        return back()->with('success', 'Sesión marcada como En Proceso.');
    }
}
