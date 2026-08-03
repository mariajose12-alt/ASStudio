<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReservaService;
use App\Models\Catalogo;
use App\Models\PaqueteFotografico;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    protected ReservaService $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Muestra el índice de las reservas del cliente.
     */
    public function index()
    {
        $reservas = $this->reservaService->reservasDelCliente(Auth::id());

        return view('cliente.reservas.index', compact('reservas'));
    }

    /**
     * Paso 1: Selección de Catálogo, Paquete, Tipo y Lugar.
     */
    public function paso1()
    {
        // Traemos los catálogos activos junto con sus paquetes
        $catalogos = Catalogo::with(['paquetes' => function($query) {
            $query->where('activo', true);
        }])->where('activo', true)->get();

        $p1 = session('reserva.paso1', []);
        $requiereTelefono = $this->requiereTelefono();

        return view('reservas.paso1', compact('catalogos', 'p1', 'requiereTelefono'));
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

        session(['reserva.paso1' => $request->only(['catalogo_id', 'paquete_id', 'tipo', 'lugar'])]);

        return redirect()->route('cliente.reservas.paso2');
    }

    /**
     * Paso 2: Selección de Fecha y Hora.
     */
    public function paso2()
    {
        if (!session()->has('reserva.paso1')) {
            return redirect()->route('cliente.reservas.paso1')->with('error', 'Por favor completa el primer paso.');
        }

        $requiereTelefono = $this->requiereTelefono();

        return view('reservas.paso2', compact('requiereTelefono'));
    }

    public function guardarPaso2(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:' . now()->addDay()->format('Y-m-d'),
            'hora'        => 'required|date_format:H:i',
            'descripcion' => 'required|string|max:600',
        ]);

        session(['reserva.paso2' => $request->only(['fecha', 'hora', 'descripcion'])]);

        return redirect()->route('cliente.reservas.paso3');
    }

    /**
     * Paso 3: Confirmación de datos y adición de detalles extras.
     */
    public function paso3()
    {
        if (!session()->has('reserva.paso2')) {
            return redirect()->route('cliente.reservas.paso2')->with('error', 'Por favor selecciona la fecha y hora de tu sesión.');
        }

        $usuario = Auth::user();
        $usuario->load('persona');

        // Si ya tiene teléfono registrado, saltamos el paso automáticamente
        if (!empty($usuario->persona?->telefono)) {
            session(['reserva.paso3' => [
                'nombre'          => trim($usuario->persona->nombre . ' ' . $usuario->persona->apellido),
                'correo'          => $usuario->email,
                'telefono'        => $usuario->persona->telefono,
                'acepta_whatsapp' => (bool) $usuario->persona->acepta_whatsapp,
            ]]);

            return redirect()->route('cliente.reservas.paso4');
        }

        $requiereTelefono = true;

        return view('reservas.paso3', compact('usuario', 'requiereTelefono'));
    }

    // Solo guardar en sesion, al enviar se actualiza la BD
    public function guardarPaso3(Request $request)
    {
        $request->validate([
            'telefono'    => 'nullable|string|max:20',
        ]);

        $usuario = Auth::user();
        $usuario->loadMissing('persona');

        session(['reserva.paso3' => [
            'nombre'          => trim($usuario->persona->nombre . ' ' . $usuario->persona->apellido),
            'correo'          => $usuario->email,
            'telefono'        => $request->telefono,
            'acepta_whatsapp' => $request->boolean('acepta_whatsapp'),
        ]]);

        return redirect()->route('cliente.reservas.paso4');
    }

    /**
     * Paso 4: Resumen final de la reserva.
     */
    public function paso4()
    {
        if (!session()->has('reserva.paso3')) {
            return redirect()->route('cliente.reservas.paso3')->with('error', 'Por favor verifica tus datos de contacto.');
        }

        $paso1 = session('reserva.paso1');
        $paso2 = session('reserva.paso2');
        $paso3 = session('reserva.paso3');

        $paquete = PaqueteFotografico::with('catalogos')->find($paso1['paquete_id']);
        $requiereTelefono = $this->requiereTelefono();

        return view('reservas.paso4', compact('paso1', 'paso2', 'paso3', 'paquete', 'requiereTelefono'));
    }

    /**
     * Procesamiento final: Envío al Servicio y persistencia en BD.
     */
    public function enviar()
    {
        $paso1 = session('reserva.paso1');
        $paso2 = session('reserva.paso2');
        $paso3 = session('reserva.paso3', []);

        if (!$paso1 || !$paso2) {
            return redirect()
                ->route('cliente.reservas.paso1')
                ->with('error', 'Faltan datos para completar la reserva.');
        }

        // Actualizar datos del usuario SOLO al confirmar
        if (!empty($paso3['telefono'])) {
            Auth::user()->persona->update([
                'telefono'        => $paso3['telefono'],
                'acepta_whatsapp' => $paso3['acepta_whatsapp'] ?? false,
            ]);
        }

        try {

            $reserva = $this->reservaService->crearReserva(
                paso1: $paso1,
                paso2: $paso2,
                usuario_id: auth()->id(),
                paso3: $paso3
            );

            session()->forget('reserva');

            return redirect()
                ->route('cliente.reservas.index')
                ->with('success', '¡Reserva solicitada exitosamente!');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Ocurrió un error al procesar tu reserva. Intenta de nuevo.');
        }
    }

    private function requiereTelefono(): bool
    {
        $usuario = Auth::user();
        $usuario->loadMissing('persona');

        return empty($usuario->persona?->telefono);
    }
}
