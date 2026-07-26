<?php

namespace App\Http\Controllers;

use App\Models\SolicitudEstudio;
use App\Services\SolicitudEstudioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// Protegido en routes/web.php con middleware(['auth', 'rol:socio_estudio'])
// — igual que CheckRol ya protege /admin/*, no un check manual acá para
// evitar duplicar lógica de autorización en dos lugares (fuente de bugs).
class SolicitudEstudioAdminController extends Controller
{
    public function __construct(private SolicitudEstudioService $service)
    {
    }

    public function index(): View
    {
        $solicitudes = SolicitudEstudio::orderBy('fecha')->paginate(20);

        return view('admin.estudio.solicitudes.index', compact('solicitudes'));
    }

    public function show(SolicitudEstudio $solicitud): View
    {
        $view = request()->routeIs('socio.*')
            ? 'socio.estudio.solicitudes.show'
            : 'admin.estudio.solicitudes.show';

        return view($view, compact('solicitud'));
    }

    public function aprobar(Request $request, SolicitudEstudio $solicitud): RedirectResponse
    {
        $request->validate(['nota' => ['nullable', 'string', 'max:500']]);
        $this->service->aprobar($solicitud, Auth::user(), $request->input('nota'));
        return $this->volver($request, 'Solicitud aprobada.');
    }

    public function rechazar(Request $request, SolicitudEstudio $solicitud): RedirectResponse
    {
        $request->validate(['nota' => ['nullable', 'string', 'max:500']]);
        $this->service->rechazar($solicitud, Auth::user(), $request->input('nota'));
        return $this->volver($request, 'Solicitud rechazada.');
    }

    private function volver(Request $request, string $mensaje): RedirectResponse
    {
        if ($request->filled('volver_tab')) {
            return redirect()->route('socio.estudio.dashboard', ['tab' => $request->input('volver_tab')])
                ->with('success', $mensaje);
        }

        return back()->with('success', $mensaje);
    }
}
