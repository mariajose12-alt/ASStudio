<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ClienteReservaController extends Controller
{
    public function index()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $reservas = $cliente
            ? $cliente->reservas()->with(['paquete', 'catalogo'])->latest()->get()
            : collect();

        return view('cliente.reservas.index', compact('reservas'));
    }

//    public function show(Reserva $reserva)
//    {
//        // Evita que un cliente vea la reserva de otro
//        $cliente = Cliente::where('usuario_id', auth()->id())->firstOrFail();
//        abort_if($reserva->cliente_id !== $cliente->id, 403);
//
//        $reserva->load(['paquete', 'catalogo']);
//
//        return view('cliente.reservas.show', compact('reserva'));
//    }
}
