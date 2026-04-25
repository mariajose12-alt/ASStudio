<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class FotografoController extends Controller
{
    public function dashboard()
    {
        $usuario   = Auth::user();
        $fotografo = $usuario->empleado->fotografo;

        $sesionesProximas = $fotografo->sesiones()
            ->wherePivot('estado_participacion', '!=', 'CANCELADA')
            ->where('fecha_inicio', '>=', now())
            ->orderBy('fecha_inicio')
            ->limit(5)
            ->get();

        $totalSesiones    = $fotografo->sesiones()->count();
        $sesionesPendientes = $fotografo->sesiones()
            ->wherePivot('estado_participacion', 'PENDIENTE')
            ->count();

        return view('fotografo.dashboard', compact(
            'fotografo',
            'sesionesProximas',
            'totalSesiones',
            'sesionesPendientes',
        ));
    }
}
