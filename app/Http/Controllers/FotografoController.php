<?php

namespace App\Http\Controllers;

use App\Services\FotografoService;
use Illuminate\Support\Facades\Auth;

class FotografoController extends Controller
{
    public function __construct(
        private FotografoService $fotografoService
    ) {}

    public function dashboard()
    {
        $fotografo = Auth::user()->empleado->fotografo;
        $metricas  = $this->fotografoService->metricasDashboard($fotografo);

        return view('fotografo.dashboard', array_merge(
            compact('fotografo'),
            $metricas
        ));
    }

    public function calendario()
    {
        return view('fotografo.calendario');
    }

    public function upload()
    {
        return view('fotografo.upload');
    }

    public function reservasJson()
    {
        $fotografo = Auth::user()->empleado->fotografo;
        $eventos   = $this->fotografoService->eventosCalendario($fotografo);

        return response()->json($eventos);
    }

}
