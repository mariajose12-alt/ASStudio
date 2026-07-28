<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Reserva;
use App\Services\FotografoReservaService;
use Illuminate\Http\Request;

class ClienteReservaController extends Controller
{
    public function __construct(
        private FotografoReservaService $reservaService
    ) {}

    public function index()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $reservas = $cliente
            ? $cliente->reservas()->with(['paquete', 'catalogo'])->latest()->get()
            : collect();

        return view('cliente.reservas.index', compact('reservas'));
    }

    public function show(Reserva $reserva)
    {
        $this->autorizarPropietario($reserva);

        $reserva->load(['paquete', 'catalogo', 'sesion', 'pagos']);

        $lineaTiempo = $this->construirLineaTiempo($reserva);

        return view('cliente.reservas.show', compact('reserva', 'lineaTiempo'));
    }

    public function responderSugerencia(Request $request, Reserva $reserva)
    {
        $this->autorizarPropietario($reserva);

        $request->validate([
            'accion'      => 'required|in:ACEPTAR,EDITAR,CANCELAR',
            'descripcion' => 'required_if:accion,EDITAR|nullable|string|max:1000',
            'nueva_fecha' => 'nullable|date',
            'nueva_hora'  => 'nullable|date_format:H:i',
        ]);

        try {
            if ($request->accion === 'EDITAR' && $request->nueva_fecha && $request->nueva_hora) {
                $this->validarDisponibilidadFotografo($reserva, $request->nueva_fecha, $request->nueva_hora);
            }

            match ($request->accion) {
                'ACEPTAR'  => $this->reservaService->aprobarPorAceptacionCliente($reserva),
                'CANCELAR' => $reserva->cancelar(),
                'EDITAR'   => $reserva->reenviarParaRevision(
                    $request->descripcion,
                    $request->nueva_fecha,
                    $request->nueva_hora,
                ),
            };
        } catch (\App\Exceptions\NegocioException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Ocurrió un error al procesar tu respuesta. Intenta de nuevo.');
        }

        return redirect()->route('cliente.reservas.index')
            ->with('success', match($request->accion) {
                'ACEPTAR'  => 'Sugerencia aceptada. Tu sesión quedó confirmada.',
                'CANCELAR' => 'Reserva cancelada.',
                'EDITAR'   => 'Reserva reenviada para revisión.',
            });
    }

    private function autorizarPropietario(Reserva $reserva): void
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->firstOrFail();
        abort_if($reserva->cliente_id !== $cliente->id, 403);
    }

    private function validarDisponibilidadFotografo(Reserva $reserva, string $nuevaFecha, string $nuevaHora): void
    {
        $fechaHora    = \Carbon\Carbon::parse("$nuevaFecha $nuevaHora");
        $fechaHoraFin = $fechaHora->copy()->addHours($reserva->duracion_horas_propuesta ?? 2.0);

        $conflicto = Reserva::where('fotografo_id', $reserva->fotografo_id)
            ->where('id', '!=', $reserva->id)
            ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->where('fecha_inicio', '<', $fechaHoraFin)
            ->where('fecha_fin',    '>', $fechaHora)
            ->exists();

        if ($conflicto) {
            throw new \App\Exceptions\NegocioException('Esa fecha ya no está disponible para el fotógrafo, elige otra.');
        }
    }

    /**
     * Construye el timeline de progreso del cliente combinando el estado
     * de la Reserva, la Sesion y AMBOS pagos (anticipo + final).
     */
    private function construirLineaTiempo(Reserva $reserva): array
    {
        $sesion = $reserva->sesion;
        $pagoAnticipo = $reserva->pagos->whereIn('tipo', ['ANTICIPO', 'COMPLETO'])->sortByDesc('id')->first();
        $pagoFinal    = $reserva->pagos->where('tipo', 'FINAL')->sortByDesc('id')->first();

        if ($reserva->estado === 'RECHAZADA') {
            return [
                'especial' => ['tipo' => 'rechazada', 'titulo' => 'Reserva rechazada', 'descripcion' => $reserva->motivo_rechazo ?: 'El fotógrafo no pudo aceptar esta solicitud.'],
                'pasos' => [],
            ];
        }

        if ($reserva->estado === 'CANCELADA') {
            return [
                'especial' => ['tipo' => 'cancelada', 'titulo' => 'Reserva cancelada', 'descripcion' => 'Esta reserva fue cancelada y ya no está activa.'],
                'pasos' => [],
            ];
        }

        $especial = null;
        if ($reserva->estado === 'MODIFICACION_PROPUESTA') {
            $especial = ['tipo' => 'modificacion', 'titulo' => 'El fotógrafo propone un cambio', 'descripcion' => $reserva->motivo_rechazo];
        }

        // ── 7 escalones fijos ──
        $pasos = [
            ['clave' => 'revision',    'titulo' => 'Revisión y aprobación', 'descripcion' => 'El fotógrafo revisa los detalles de tu solicitud.'],
            ['clave' => 'anticipo',    'titulo' => 'Pago del anticipo',      'descripcion' => 'Confirmas tu fecha con el 50% de anticipo.'],
            ['clave' => 'sesion',      'titulo' => 'Sesión fotográfica',     'descripcion' => 'El día de tu sesión, en el lugar acordado.'],
            ['clave' => 'seleccion',   'titulo' => 'Selección de fotos',     'descripcion' => 'Eliges cuáles fotos originales quieres que editemos.'],
            ['clave' => 'pago_final',  'titulo' => 'Segundo pago',           'descripcion' => 'Completas el pago restante de tu sesión.'],
            ['clave' => 'edicion',     'titulo' => 'Edición',                'descripcion' => 'El fotógrafo edita las fotos que seleccionaste.'],
            ['clave' => 'final',       'titulo' => 'Galería final',          'descripcion' => 'Descargas tus fotos ya editadas.'],
        ];

        $indiceActual = 0;

        if ($reserva->estado === 'PENDIENTE') {
            $pasos[0]['estado'] = 'actual';
        } elseif ($reserva->estado === 'MODIFICACION_PROPUESTA') {
            $pasos[0]['estado'] = 'alerta';
        } elseif ($reserva->estado === 'APROBADA') {
            $pasos[0]['estado'] = 'completado';

            $estadoAnticipo = $pagoAnticipo?->estado;

            if (!$pagoAnticipo || $estadoAnticipo === 'PENDIENTE') {
                $indiceActual = 1;
                $pasos[1]['estado'] = 'actual';
                $pasos[1]['accion'] = ['label' => 'Ir a pagar', 'url' => route('cliente.pagos.index')];
            } elseif ($estadoAnticipo === 'EN_REVISION') {
                $indiceActual = 1;
                $pasos[1]['estado'] = 'actual';
                $pasos[1]['descripcion'] = 'Tu comprobante está en revisión — normalmente toma menos de 24 horas.';
            } elseif ($estadoAnticipo === 'RECHAZADO') {
                $indiceActual = 1;
                $pasos[1]['estado'] = 'alerta';
                $pasos[1]['descripcion'] = $pagoAnticipo->motivo_rechazo ?: 'Tu comprobante fue rechazado, sube uno nuevo.';
                $pasos[1]['accion'] = ['label' => 'Subir nuevo comprobante', 'url' => route('cliente.pagos.comprobante.form', $pagoAnticipo->id)];
            } else {
                $pasos[1]['estado'] = 'completado';

                if (!$sesion) {
                    $indiceActual = 2;
                    $pasos[2]['estado'] = 'actual';
                    $pasos[2]['descripcion'] = 'Estamos organizando los detalles de tu sesión.';
                } elseif (in_array($sesion->estado, ['CONFIRMADA', 'EN_PROCESO'])) {
                    $indiceActual = 2;
                    $pasos[2]['estado'] = 'actual';
                } elseif ($sesion->estado === 'GALERIA_DISPONIBLE') {
                    $pasos[2]['estado'] = 'completado';
                    $indiceActual = 3;
                    $pasos[3]['estado'] = 'actual';
                    $pasos[3]['accion'] = ['label' => 'Seleccionar fotos', 'url' => route('cliente.galeria.show', $sesion->id)];
                } else {
                    // EN_EDICION, FINALIZADA o CERRADA: la selección ya se hizo,
                    // así que el segundo pago entra en juego (creado junto con la selección).
                    $pasos[2]['estado'] = 'completado';
                    $pasos[3]['estado'] = 'completado';

                    if (!$pagoFinal) {
                        // Pagó completo desde el inicio: no hay segundo pago que hacer
                        $pasos[4]['estado'] = 'completado';
                        $pasos[4]['descripcion'] = 'Ya pagaste el total, no necesitas un segundo pago.';

                        if ($sesion->estado === 'EN_EDICION') {
                            $indiceActual = 5;
                        } else {
                            $indiceActual = 6;
                        }
                    } else {
                        $estadoFinal = $pagoFinal->estado;

                        if ($estadoFinal === 'PENDIENTE') {
                            $indiceActual = 4;
                            $pasos[4]['estado'] = 'actual';
                            $pasos[4]['accion'] = ['label' => 'Ir a pagar', 'url' => route('cliente.pagos.index')];
                        } elseif ($estadoFinal === 'EN_REVISION') {
                            $indiceActual = 4;
                            $pasos[4]['estado'] = 'actual';
                            $pasos[4]['descripcion'] = 'Tu comprobante está en revisión — normalmente toma menos de 24 horas.';
                        } elseif ($estadoFinal === 'RECHAZADO') {
                            $indiceActual = 4;
                            $pasos[4]['estado'] = 'alerta';
                            $pasos[4]['descripcion'] = $pagoFinal->motivo_rechazo ?: 'Tu comprobante fue rechazado, sube uno nuevo.';
                            $pasos[4]['accion'] = ['label' => 'Subir nuevo comprobante', 'url' => route('cliente.pagos.comprobante.form', $pagoFinal->id)];
                        } else {
                            $pasos[4]['estado'] = 'completado';
                            $indiceActual = $sesion->estado === 'EN_EDICION' ? 5 : 6;
                        }
                    }

                    if (in_array($sesion->estado, ['FINALIZADA', 'CERRADA'])) {
                        $pasos[6]['accion'] = ['label' => 'Ver galería final', 'url' => route('cliente.galeria.final', $sesion->id)];
                    }

                    foreach ($pasos as $i => &$paso) {
                        if ($i < 5) continue;
                        $paso['estado'] = $paso['estado'] ?? ($i < $indiceActual ? 'completado' : ($i === $indiceActual ? 'actual' : 'pendiente'));
                    }
                    unset($paso);
                }
            }
        }

        foreach ($pasos as &$paso) {
            $paso['estado'] = $paso['estado'] ?? 'pendiente';
        }
        unset($paso);

        return compact('especial', 'pasos');
    }
}
