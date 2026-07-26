<?php

namespace App\Http\Controllers;

use App\DTOs\SolicitudEstudioDTO;
use App\Http\Requests\SolicitudEstudioStoreRequest;
use App\Services\SolicitudEstudioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudEstudioController extends Controller
{
    public function __construct(private SolicitudEstudioService $service)
    {
    }

    /**
     * Página pública del wizard de reserva, enlazada desde la landing.
     */
    public function crearForm(): View
    {
        return view('estudio.reserva');
    }

    /**
     * Público, sin auth — formulario en la landing.
     * Rate-limited en routes/web.php (lección CRIT-2: no repetir login/register sin throttle).
     */
    public function store(SolicitudEstudioStoreRequest $request): JsonResponse
    {
        $dto = SolicitudEstudioDTO::fromArray($request->validated());

        try {
            $solicitud = $this->service->crear($dto);
        } catch (\App\Exceptions\NegocioException $e) {
            return response()->json(['errors' => ['hora_inicio' => [$e->getMessage()]]], 422);
        }

        return response()->json([
            'mensaje' => 'Solicitud enviada. Te contactaremos para confirmar.',
            'id' => $solicitud->id,
        ], 201);
    }

    /**
     * Público, sin auth — para pintar el calendario de disponibilidad en la landing.
     * Solo expone horarios ocupados, ningún dato del cliente (evita fuga tipo H-1).
     */
    public function disponibilidad(Request $request): JsonResponse
    {
        $request->validate(['fecha' => ['required', 'date']]);

        return response()->json(
            $this->service->disponibilidad($request->query('fecha'))
        );
    }
}
