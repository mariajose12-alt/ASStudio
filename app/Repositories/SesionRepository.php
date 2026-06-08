<?php

namespace App\Repositories;

use App\Models\Sesion;
use App\Repositories\Contracts\SesionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SesionRepository implements SesionRepositoryInterface
{
    public function __construct(protected Sesion $model) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['reserva.cliente.usuario.persona', 'fotografos.empleado.usuario.persona'])
            ->latest('fecha_inicio')
            ->get();
    }

    public function find(int $id): ?Sesion
    {
        return $this->model
            ->with(['reserva.cliente.usuario.persona', 'fotografos.empleado.usuario.persona', 'participaciones'])
            ->find($id);
    }

    public function create(array $data): Sesion
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $sesion = $this->model->find($id);

        if (!$sesion) {
            return false;
        }

        return $sesion->update($data);
    }

    public function delete(int $id): bool
    {
        $sesion = $this->model->find($id);

        if (!$sesion) {
            return false;
        }

        return $sesion->delete();
    }

    public function porEstado(string $estado): Collection
    {
        return Sesion::where('estado', $estado)
            ->with(['reserva.cliente.usuario.persona', 'fotografos.empleado.usuario.persona'])
            ->latest('fecha_inicio')
            ->get();
    }

    public function porFotografo(int $fotografoId): Collection
    {
        return Sesion::whereHas('fotografos', fn($q) => $q->where('fotografos.id', $fotografoId))
            ->with(['reserva.cliente.usuario.persona', 'reserva.paquete'])
            ->latest('fecha_inicio')
            ->get();
    }

    public function porReserva(int $reservaId): ?Sesion
    {
        return Sesion::where('reserva_id', $reservaId)
            ->with(['fotografos.empleado.usuario.persona', 'participaciones'])
            ->first();
    }

    public function galeriaDisponible(): Collection
    {
        return Sesion::where('estado', 'GALERIA_DISPONIBLE')
            ->with(['reserva.cliente.usuario.persona', 'reserva.paquete'])
            ->latest('fecha_inicio')
            ->get();
    }
}
