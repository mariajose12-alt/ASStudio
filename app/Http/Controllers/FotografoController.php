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
        return view('fotografo.dashboard', $this->fotografoService->getData());
    }

    public function calendario()
    {
        return view('fotografo.calendario');
    }

    public function upload()
    {
        return view('fotografo.fotografias');
    }

    public function reservasJson()
    {
        $fotografo = Auth::user()->empleado->fotografo;
        $eventos   = $this->fotografoService->eventosCalendario($fotografo);

        return response()->json($eventos);
    }

}
