<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\PaqueteFotografico;
use App\Models\Catalogo;
use App\Models\Reserva;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalEmpleados  = Empleado::count();
        $totalPaquetes   = PaqueteFotografico::count();
        $totalCatalogos  = Catalogo::count();
        $totalReservas   = Reserva::count();
        $reservasPendientes = Reserva::where('estado', 'PENDIENTE')->count();

        return view('admin.dashboard', compact(
            'totalEmpleados',
            'totalPaquetes',
            'totalCatalogos',
            'totalReservas',
            'reservasPendientes'
        ));
    }

    public function reservasIndex()
    {
        $reservas = Reserva::with(['cliente.usuario', 'paquete'])
            ->latest()
            ->paginate(15);

        return view('admin.reservas.index', compact('reservas'));
    }

    public function reservasShow(Reserva $reserva)
    {
        $reserva->load(['cliente.usuario', 'paquete.catalogos']);
        return view('admin.reservas.show', compact('reserva'));
    }

    public function reservasCambiarEstado(\Illuminate\Http\Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => 'required|in:APROBADA,RECHAZADA,CANCELADA,PENDIENTE,PAGO_RECIBIDO'
        ]);

        $reserva->update(['estado' => $request->estado]);

        return redirect()->route('admin.reservas.show', $reserva)
            ->with('success', 'Estado de la reserva actualizado correctamente.');
    }
}
