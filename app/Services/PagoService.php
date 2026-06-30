<?php

namespace App\Services;

use App\DTOs\PagoRegistrarDTO;
use App\Events\PagoConfirmado;
use App\Models\Comprobante;
use App\Models\Pago;
use App\Models\Reserva;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PagoService
{
    const PORCENTAJE_ANTICIPO = 0.50;

    public function registrarAnticipo(Reserva $reserva, PagoRegistrarDTO $dto): Pago
    {
        $this->validarReservaParaPago($reserva);

        if ($this->tieneAnticipoPendienteOConfirmado($reserva)) {
            throw new Exception('Esta reserva ya tiene un anticipo registrado.');
        }

        $monto = round($reserva->precio_total * self::PORCENTAJE_ANTICIPO, 2);

        return Pago::create([
            'reserva_id' => $reserva->id,
            'cliente_id' => $reserva->cliente_id,
            'monto'      => $monto,
            'metodo'     => $dto->metodo,
            'tipo'       => 'ANTICIPO',
            'estado'     => 'PENDIENTE',
        ]);
    }

    public function registrarPagoFinal(Reserva $reserva, PagoRegistrarDTO $dto): Pago
    {
        $this->validarReservaParaPago($reserva);

        $montoAnticipo = Pago::where('reserva_id', $reserva->id)
            ->where('tipo', 'ANTICIPO')
            ->where('estado', 'CONFIRMADO')
            ->sum('monto');

        if ($montoAnticipo <= 0) {
            throw new Exception('Debe confirmarse el anticipo antes de registrar el pago final.');
        }

        $monto = round($reserva->precio_total - $montoAnticipo, 2);

        if ($monto <= 0) {
            throw new Exception('El anticipo ya cubre el total de la reserva.');
        }

        return Pago::create([
            'reserva_id' => $reserva->id,
            'cliente_id' => $reserva->cliente_id,
            'monto'      => $monto,
            'metodo'     => $dto->metodo,
            'tipo'       => 'FINAL',
            'estado'     => 'PENDIENTE',
        ]);
    }

    public function adjuntarComprobante(Pago $pago, UploadedFile $archivo): Pago
    {
        if (!$pago->estaPendiente()) {
            throw new Exception('Solo se puede adjuntar comprobante a pagos en estado PENDIENTE.');
        }

        $path = Storage::disk('r2')->putFile(
            "comprobantes/{$pago->reserva_id}",
            $archivo
        );

        if (!$path) {
            throw new Exception('Error al subir el comprobante al almacenamiento.');
        }

        $comprobante = Comprobante::create([
            'url_archivo'    => $path,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'validado'       => false,
        ]);

        $pago->update(['comprobante_id' => $comprobante->id]);

        return $pago->fresh(['comprobante']);
    }

    public function confirmarPago(Pago $pago): Pago
    {
        if ($pago->estaConfirmado()) {
            throw new Exception('El pago ya fue confirmado.');
        }

        if (!$pago->comprobante_id) {
            throw new Exception('No hay comprobante adjunto. No se puede confirmar el pago.');
        }

        $pago->update([
            'estado'           => 'CONFIRMADO',
            'fecha_completado' => now(),
        ]);

        $pago->comprobante->update(['validado' => true]);

        $reserva = $pago->reserva;

        if ($pago->esAnticipo() && $reserva->estado === 'APROBADA') {
            $reserva->update(['estado' => 'CONFIRMADA']);
        }

        PagoConfirmado::dispatch($reserva->fresh());

        return $pago->fresh(['reserva', 'comprobante']);
    }

    public function rechazarPago(Pago $pago): Pago
    {
        if ($pago->estaConfirmado()) {
            throw new Exception('No se puede rechazar un pago ya confirmado.');
        }

        $pago->update(['estado' => 'RECHAZADO']);

        return $pago->fresh();
    }

    private function validarReservaParaPago(Reserva $reserva): void
    {
        $estadosValidos = ['APROBADA', 'CONFIRMADA'];

        if (!in_array($reserva->estado, $estadosValidos)) {
            throw new Exception(
                "La reserva debe estar APROBADA o CONFIRMADA para registrar un pago. Estado actual: {$reserva->estado}"
            );
        }
    }

    private function tieneAnticipoPendienteOConfirmado(Reserva $reserva): bool
    {
        return Pago::where('reserva_id', $reserva->id)
            ->where('tipo', 'ANTICIPO')
            ->whereIn('estado', ['PENDIENTE', 'CONFIRMADO'])
            ->exists();
    }
}
