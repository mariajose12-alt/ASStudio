<?php

namespace App\Services;

use App\DTOs\ReservaCreateDTO;
use App\Events\ReservaCreada;
use App\Http\Controllers\DisponibilidadController;
use App\Models\Cliente;
use App\Models\Fotografo;
use App\Models\PaqueteFotografico;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Models\Reserva;
use Exception;

class ReservaService
{
    public function __construct(
        private ReservaRepositoryInterface $reservaRepository
    ) {}

    public function crearReserva(array $paso1, array $paso2, int $usuario_id, array $paso3 = []): Reserva
    {
        $fecha = $paso2['fecha'];
        $hora  = $paso2['hora'];
        $fechaHora = \Carbon\Carbon::parse("$fecha $hora");

        // Fotógrafos que tienen agenda que cubre ese horario
        // y no tienen otra reserva en ese slot
        $disponibles = Fotografo::whereDoesntHave('agenda', function ($q) use ($fechaHora) {

            $q->where('fecha_inicio', '<=', $fechaHora)
                ->where('fecha_fin',    '>=', $fechaHora);
        })->whereDoesntHave('reservas', function ($q) use ($fechaHora) {
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', $fechaHora);
            })
            ->get();


        if ($disponibles->isEmpty()) {
            throw new Exception(
                'No hay fotógrafos disponibles en esa fecha y hora.'
            );
        }

        $fotografo = $disponibles->count() === 1
            ? $disponibles->first()
            : $disponibles->random();

        $paquete = PaqueteFotografico::findOrFail($paso1['paquete_id']);
        $cliente = Cliente::firstOrCreate(['usuario_id' => $usuario_id]);

        // El usuario solo modifica en el paso3 si es necesario, si paso3 viene vacio no se actualiza nada
        if (!empty($paso3)) {

            $cliente->update([
                'telefono' => $paso3['telefono'] ?? $cliente->telefono,
                'direccion' => $paso3['direccion'] ?? $cliente->direccion,
                'nombre'    => $paso3['nombre'] ?? $cliente->nombre,
            ]);
        }

        $dto = ReservaCreateDTO::fromSesion(
            paso1:        $paso1,
            paso2:        $paso2,
            cliente_id:   $cliente->id,
            fotografo_id: $fotografo->id,
            precio_total: $paquete->precio_base,
        );

        $reserva = $this->reservaRepository->crear($dto);

        //comentado por ahora -- funciona
        //ReservaCreada::dispatch($reserva);

        return $reserva;
    }

    public function reservasDelCliente(int $usuario_id)
    {
        $cliente = Cliente::where('usuario_id', $usuario_id)->first();

        if (!$cliente) return collect();

        return $this->reservaRepository->porCliente($cliente->id);
    }
}
