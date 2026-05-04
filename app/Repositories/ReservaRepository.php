<?php

namespace App\Repositories;

use App\DTOs\ReservaCreateDTO;
use App\Models\Reserva;
use App\Repositories\Contracts\ReservaRepositoryInterface;

class ReservaRepository implements ReservaRepositoryInterface
{
    public function crear(ReservaCreateDTO $dto): Reserva
    {
        return Reserva::create([
            'cliente_id'   => $dto->cliente_id,
            'paquete_id'   => $dto->paquete_id,
            'catalogo_id'  => $dto->catalogo_id,
            'fotografo_id' => $dto->fotografo_id,
            'tipo'         => $dto->tipo,
            'lugar'        => $dto->lugar,
            'descripcion'  => $dto->descripcion,
            'fecha_inicio' => $dto->fecha_inicio,
            'fecha_fin'    => $dto->fecha_fin,
            'estado'       => 'PENDIENTE',
            'precio_total' => $dto->precio_total,
        ]);
    }

    public function porCliente(int $cliente_id)
    {
        return Reserva::where('cliente_id', $cliente_id)
            ->with('paquete')
            ->latest()
            ->get();
    }

    public function hayDisponibilidad(string $fecha, string $hora): bool
    {
        return !Reserva::where('fecha_inicio', $fecha . ' ' . $hora)
            ->whereIn('estado', ['PENDIENTE', 'APROBADA', 'CONFIRMADA'])
            ->exists();
    }

}
