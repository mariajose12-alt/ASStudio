<?php

namespace App\Http\Controllers;

use App\Models\PostulacionAyudante;
use App\Models\Sesion;
use App\Models\SolicitudAyudante;
use App\Services\SolicitudAyudanteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SolicitudAyudanteController extends Controller
{
    public function __construct(private SolicitudAyudanteService $service) {}

    public function store(Request $request, Sesion $sesion)
    {
        $request->validate([
            'cantidad_ayudantes' => 'required|integer|min:1|max:10',
            'mensaje'            => 'nullable|string|max:500',
        ]);

        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->crear(
                $sesion,
                $fotografo,
                (int) $request->cantidad_ayudantes,
                $request->mensaje
            );

            return back()->with('success', 'Solicitud de ayudante enviada. Se notificó a los fotógrafos disponibles.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function postularse(SolicitudAyudante $solicitud)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->postular($solicitud, $fotografo);

            return back()->with('success', 'Te postulaste correctamente. El fotógrafo principal confirmará su selección.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function confirmar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->confirmar($solicitud, $postulacion, $fotografo);

            return back()->with('success', 'Ayudante confirmado.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rechazar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->rechazar($solicitud, $postulacion, $fotografo);

            return back()->with('success', 'Postulación rechazada.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancelar(SolicitudAyudante $solicitud)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->cancelar($solicitud, $fotografo);

            return back()->with('success', 'Solicitud cancelada. Los ayudantes ya confirmados se mantienen.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
