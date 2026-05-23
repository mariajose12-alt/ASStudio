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

        return view('reservas.paso1', compact('catalogos'));
    }

    public function guardarPaso1(Request $request)
    {
        $request->validate([
            'catalogo_id' => 'required|exists:catalogos,id',
            'paquete_id'  => 'required|exists:paquetes_fotograficos,id',
            'tipo'        => 'required|in:ESTUDIO,EXTERIOR',
            'lugar'       => 'nullable|string|max:255|required_if:tipo,EXTERIOR',
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

        return view('reservas.paso2');
    }

    public function guardarPaso2(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'hora'  => 'required|date_format:H:i',
        ]);

        session(['reserva.paso2' => $request->only(['fecha', 'hora'])]);

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

        return view('reservas.paso3', compact('usuario'));
    }

    public function guardarPaso3(Request $request)
    {
        // TSK-60: Se añadió la validación de 'descripcion'
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'correo'      => 'required|email|max:150',
            'telefono'    => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        // TSK-60: Se guarda la descripción en la sesión para el DTO
        session(['reserva.paso3' => $request->only(['nombre', 'correo', 'telefono', 'descripcion'])]);

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

        $paquete = PaqueteFotografico::find($paso1['paquete_id']);

        return view('reservas.paso4', compact('paso1', 'paso2', 'paso3', 'paquete'));
    }

    /**
     * Procesamiento final: Envío al Servicio y persistencia en BD.
     */
    public function enviar()
    {
        $paso1 = session('reserva.paso1');
        $paso2 = session('reserva.paso2');
        $paso3 = session('reserva.paso3', []); // TSK-60: Extraemos los datos del paso 3

        if (!$paso1 || !$paso2) {
            return redirect()->route('cliente.reservas.paso1')->with('error', 'Faltan datos para completar la reserva.');
        }

        try {
            // TSK-60: Se pasa el $paso3 como cuarto parámetro al ReservaService
            $reserva = $this->reservaService->crearReserva($paso1, $paso2, Auth::id(), $paso3);

            // Se limpia la sesión del wizard al terminar exitosamente
            session()->forget('reserva');

            return redirect()->route('cliente.reservas.index')->with('success', '¡Reserva solicitada exitosamente! Un fotógrafo la revisará pronto.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }
}
