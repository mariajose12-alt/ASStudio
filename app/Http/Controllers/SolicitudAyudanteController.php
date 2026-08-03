<?php

namespace App\Http\Controllers;

use App\Models\PostulacionAyudante;
use App\Models\Sesion;
use App\Models\SolicitudAyudante;
use App\Exceptions\NegocioException;
use Illuminate\Support\Facades\Log;
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
        } catch (NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error inesperado en solicitud de ayudante', [
                'usuario_id' => Auth::id(),
                'message'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error inesperado. Intenta de nuevo.');
        }
    }

    public function postularse(SolicitudAyudante $solicitud)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->postular($solicitud, $fotografo);

            return redirect()->route('fotografo.sesiones.index', ['tab' => 'ayudantes'])
                ->with('success', 'Te postulaste correctamente. El fotógrafo principal confirmará su selección.');
        } catch (NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error inesperado en solicitud de ayudante', [
                'usuario_id' => Auth::id(),
                'message'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error inesperado. Intenta de nuevo.');
        }
    }

    public function confirmar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->confirmar($solicitud, $postulacion, $fotografo);

            return redirect()->route('fotografo.sesiones.index', ['tab' => 'ayudantes'])
                ->with('success', 'Ayudante confirmado.');
        } catch (NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error inesperado en solicitud de ayudante', [
                'usuario_id' => Auth::id(),
                'message'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error inesperado. Intenta de nuevo.');
        }
    }

    public function rechazar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->rechazar($solicitud, $postulacion, $fotografo);

            return redirect()->route('fotografo.sesiones.index', ['tab' => 'ayudantes'])
                ->with('success', 'Postulación rechazada.');
        } catch (NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error inesperado en solicitud de ayudante', [
                'usuario_id' => Auth::id(),
                'message'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error inesperado. Intenta de nuevo.');
        }
    }

    public function cancelar(SolicitudAyudante $solicitud)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $this->service->cancelar($solicitud, $fotografo);

            return redirect()->route('fotografo.sesiones.index', ['tab' => 'ayudantes'])
                ->with('success', 'Solicitud cancelada. Los ayudantes ya confirmados se mantienen.');
        } catch (NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('Error inesperado en solicitud de ayudante', [
                'usuario_id' => Auth::id(),
                'message'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrió un error inesperado. Intenta de nuevo.');
        }
    }
}
