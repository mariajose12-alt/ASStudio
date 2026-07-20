<?php

namespace App\Services;

use App\Jobs\GenerarZipGaleria;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class GaleriaService
{
    // Queries

    public function sesionesDelCliente(int $clienteId): Collection
    {
        return Sesion::with([
            'reserva.paquete',
            'reserva.fotografo.empleado.usuario.persona',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $clienteId))
            ->whereIn('estado', ['GALERIA_DISPONIBLE', 'EN_EDICION', 'FINALIZADA'])
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    public function sesionParaSeleccion(int $sesionId, int $clienteId): Sesion
    {
        return Sesion::with([
            'reserva.paquete',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($sesionId);
    }

    public function sesionParaFinal(int $sesionId, int $clienteId): Sesion
    {
        return Sesion::with([
            'reserva.paquete',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $clienteId))
            ->findOrFail($sesionId);
    }

    public function fotosOriginalesConUrl(Sesion $sesion): Collection
    {
        return $sesion->fotografias
            ->where('estado', 'ORIGINAL')
            ->values()
            ->map(fn($foto) => $this->agregarUrlFirmada($foto));
    }

    public function fotosFinalesConUrl(Sesion $sesion): Collection
    {
        return $sesion->fotografias
            ->whereIn('estado', ['ORIGINAL', 'PENDIENTE_EDICION', 'EDITADA'])
            ->values()
            ->map(fn($foto) => $this->agregarUrlFirmada($foto));
    }

    // Acciones

    public function confirmarSeleccion(Sesion $sesion, array $fotosSeleccionadas): void
    {
        $limite        = $sesion->reserva->paquete->cantidad_fotos_incluidas;
        $seleccionadas = array_slice($fotosSeleccionadas, 0, $limite);

        Fotografia::where('sesion_id', $sesion->id)
            ->where('estado', 'ORIGINAL')
            ->update(['seleccionada' => false]);

        Fotografia::whereIn('id', $seleccionadas)
            ->where('sesion_id', $sesion->id)
            ->update([
                'seleccionada' => true,
                'estado'       => 'PENDIENTE_EDICION',
            ]);

        $sesion->update(['estado' => 'EN_EDICION']);
    }
    public function confirmarRecepcion(Sesion $sesion): void
    {
        $sesion->update([
            'estado' => 'FINALIZADA',
            'fecha_finalizacion' => now(),
        ]);
    }


    public function urlTemporalFoto(Fotografia $foto, int $minutos = 5): string
    {
        return Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes($minutos));
    }

    public function despacharZip(Sesion $sesion, string $tipo): void
    {
        $estado = match ($tipo) {
            'originales' => 'ORIGINAL',
            'editadas'   => 'EDITADA',
            default      => throw new \InvalidArgumentException("Tipo de descarga no válido: {$tipo}"),
        };

        $fotos = $sesion->fotografias->where('estado', $estado)->values();

        if ($fotos->isEmpty()) {
            throw new \RuntimeException('No hay fotos disponibles para descargar.');
        }

        GenerarZipGaleria::dispatch($sesion, $tipo, $fotos->pluck('id')->all());
    }

    // Helpers

    private function agregarUrlFirmada(Fotografia $foto, int $minutos = 60): Fotografia
    {
        $foto->url_firmada = Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes($minutos));
        return $foto;
    }
}
