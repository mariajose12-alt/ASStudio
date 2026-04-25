<?php

namespace App\Http\Controllers;

use \App\Http\Controllers\Auth;
use App\Models\Catalogo;
use App\Models\Cliente;
use App\Models\PaqueteFotografico;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{

    public function index()
    {
        $cliente = \App\Models\Cliente::where('usuario_id', auth()->id())->first();

        $reservas = $cliente
            ? $cliente->reservas()->with('paquete')->latest()->get()
            : collect();

        return view('reservas.index', compact('reservas'));
    }

    public function paso1()
    {
        $catalogos = Catalogo::where('activo', true)->get();
        return view('reservas.paso1', compact('catalogos'));
    }

    public function guardarPaso1(Request $request)
    {
        $request->validate([
            'catalogo_id' => 'required|exists:catalogos,id',
            'paquete_id'  => 'required|exists:paquetes_fotograficos,id',
            'tipo'        => 'required|in:ESTUDIO,EXTERIOR',
            'lugar'       => 'nullable|string|max:255',
        ]);

        session(['reserva_paso1' => $request->only([
            'catalogo_id', 'paquete_id', 'tipo', 'lugar'
        ])]);

        return redirect()->route('cliente.reservas.paso2');
    }

    public function paso2()
    {
        if (!session('reserva_paso1')) {
            return redirect()->route('reservas.paso1');
        }
        return view('reservas.paso2');
    }

    public function guardarPaso2(Request $request)
    {
        $request->validate([
            'fecha'       => 'required|date|after:today',
            'hora'        => 'required',
            'descripcion' => 'required|string|max:1000',
        ]);

        session(['reserva_paso2' => $request->only([
            'fecha', 'hora', 'descripcion'
        ])]);

        return redirect()->route('cliente.reservas.paso3');
    }

    public function paso3()
    {
        if (!session('reserva_paso2')) {
            return redirect()->route('reservas.paso1');
        }
        return view('reservas.paso3', ['usuario' => auth()->user()]);
    }

    public function guardarPaso3(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'correo'   => 'required|email',
            'telefono' => 'required|string|max:20',
        ]);

        session(['reserva_paso3' => $request->only([
            'nombre', 'correo', 'telefono'
        ])]);

        return redirect()->route('cliente.reservas.paso4');
    }

    public function paso4()
    {
        if (!session('reserva_paso3')) {
            return redirect()->route('reservas.paso1');
        }

        $paso1   = session('reserva_paso1');
        $paso2   = session('reserva_paso2');
        $paso3   = session('reserva_paso3');
        $paquete = PaqueteFotografico::with('catalogos')->find($paso1['paquete_id']);

        return view('reservas.paso4', compact('paso1', 'paso2', 'paso3', 'paquete'));
    }

    public function enviar(Request $request)
    {
        $paso1   = session('reserva_paso1');
        $paso2   = session('reserva_paso2');
        $paquete = PaqueteFotografico::find($paso1['paquete_id']);

        // Obtener o crear el cliente vinculado al usuario
        $cliente = Cliente::firstOrCreate(
            ['usuario_id' => auth()->id()]
        );

        Reserva::create([
            'cliente_id'   => $cliente->id,
            'paquete_id'   => $paso1['paquete_id'],
            'catalogo_id'  => $paso1['catalogo_id'],
            'tipo'         => $paso1['tipo'],
            'lugar'        => $paso1['lugar'] ?? null,
            'descripcion'  => $paso2['descripcion'],
            'fecha_inicio' => $paso2['fecha'] . ' ' . $paso2['hora'],
            'fecha_fin'    => $paso2['fecha'] . ' ' . $paso2['hora'],
            'estado'       => 'PENDIENTE',
            'precio_total' => $paquete->precio_base,
        ]);

        session()->forget(['reserva_paso1', 'reserva_paso2', 'reserva_paso3']);

        return redirect('/')->with('success', '¡Reserva enviada! El fotógrafo revisará tu solicitud.');
    }
}
