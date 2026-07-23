<?php

namespace App\Repositories;

use App\DTOs\ReservaCreateDTO;
use App\Models\Reserva;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ReservaRepository implements ReservaRepositoryInterface
{
    // ReservaRepository.php
    public function crear(ReservaCreateDTO $dto): Reserva
    {
        $reserva = new Reserva([
            'cliente_id'     => $dto->cliente_id,
            'paquete_id'     => $dto->paquete_id,
            'catalogo_id'    => $dto->catalogo_id,
            'tipo'           => $dto->tipo,
            'lugar'          => $dto->lugar,
            'descripcion'    => $dto->descripcion,
            'fecha_inicio'   => $dto->fecha_inicio,
            'fecha_fin'      => $dto->fecha_fin,
            'duracion_horas' => 2.0,
        ]);

        $reserva->fotografo_id = $dto->fotografo_id;
        $reserva->estado       = 'PENDIENTE';
        $reserva->precio_total = $dto->precio_total;

        $reserva->save();

        return $reserva;
    }

    public function porCliente(int $cliente_id): Collection
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

    public function porEstado(string $estado): Collection
    {
        return Reserva::where('estado', $estado)
            ->with(['cliente.usuario.persona', 'paquete', 'fotografo.empleado.usuario.persona'])
            ->latest()
            ->get();
    }

    public function porFotografo(int $fotografoId): Collection
    {
        return Reserva::where('fotografo_id', $fotografoId)
            ->with(['cliente.usuario.persona', 'paquete', 'sesion'])
            ->latest()
            ->get();
    }

    public function porFecha(string $fecha): Collection
    {
        return Reserva::whereDate('fecha_inicio', $fecha)
            ->with(['cliente.usuario.persona', 'paquete', 'fotografo.empleado.usuario.persona'])
            ->orderBy('fecha_inicio')
            ->get();
    }

    public function all(): Collection
    {
        return Reserva::all();
    }

    public function find(int $id): ?Model
    {
        return Reserva::find($id);
    }

    public function create(array $data): Model
    {
        return Reserva::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Reserva::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return Reserva::destroy($id) > 0;
    }
}
