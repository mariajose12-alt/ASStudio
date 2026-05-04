<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\PaqueteFotografico;
use App\Services\ReservaService;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function __construct(
        private ReservaService $reservaService
    ) {}

    public function index()
    {
        $reservas = $this->reservaService->reservasDelCliente(auth()->id());
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
            'lugar'       => 'required_if:tipo,EXTERIOR|nullable|string|max:255',
        ], [
            'lugar.required_if' => 'El campo lugar es obligatorio, para sesiones en exteriores.',
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

        $usuario = auth()->user();
        $persona = $usuario->persona;

        // Separar nombre completo en nombre y apellido
        $partes    = explode(' ', trim($request->nombre), 2);
        $nombre    = $partes[0];
        $apellido  = $partes[1] ?? $persona->apellido;

        $persona->update([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'telefono' => $request->telefono,
        ]);

        $usuario->update([
            'email' => $request->correo,
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
        try {
            $this->reservaService->crearReserva(
                paso1:      session('reserva_paso1'),
                paso2:      session('reserva_paso2'),
                usuario_id: auth()->id(),
            );

            session()->forget(['reserva_paso1', 'reserva_paso2', 'reserva_paso3']);

            return redirect('/')->with('success', '¡Reserva enviada! El fotógrafo revisará tu solicitud.');

        } catch (\Exception $e) {
            //return back()->with('error', $e->getMessage());
            dd([
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea'   => $e->getLine(),
                'paso1'   => session('reserva_paso1'),
                'paso2'   => session('reserva_paso2'),
                'usuario' => auth()->id(),
            ]);
        }
    }
}
