<?php

namespace App\Repositories;

use App\DTOs\ReservaCreateDTO;
use App\Models\Reserva;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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
        $fechaHora    = \Carbon\Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);

        return !Reserva::whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->where('fecha_inicio', '<', $fechaHoraFin)
            ->where('fecha_fin',    '>', $fechaHora)
            ->exists();
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
