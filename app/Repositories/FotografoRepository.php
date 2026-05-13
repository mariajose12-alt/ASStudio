<?php

namespace App\Repositories;

use App\Models\Fotografo;
use App\Repositories\Contracts\FotografoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FotografoRepository implements FotografoRepositoryInterface
{
    protected Fotografo $model;

    public function __construct(Fotografo $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        // Cargamos la jerarquía completa de relaciones para tener los datos de la persona a la mano.
        return $this->model->with(['empleado.usuario.persona'])->get();
    }

    public function find(int $id): ?Fotografo
    {
        return $this->model->with(['empleado.usuario.persona'])->find($id);
    }

    public function create(array $data): Fotografo
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $fotografo = $this->model->find($id);

        if (!$fotografo) {
            return false;
        }

        return $fotografo->update($data);
    }

    public function delete(int $id): bool
    {
        $fotografo = $this->model->find($id);

        if (!$fotografo) {
            return false;
        }

        return $fotografo->delete();
    }

    public function listarActivos(): Collection
    {
        return $this->model->with(['empleado.usuario.persona'])
            ->whereHas('empleado.usuario', function ($query) {
                // Filtramos por el estado ACTIVO en la tabla usuarios
                $query->where('estado', 'ACTIVO');
            })
            ->get();
    }

    public function buscarConReservasEnPeriodo(string $fechaInicio, string $fechaFin): Collection
    {
        return $this->model->with(['empleado.usuario.persona', 'reservas' => function ($query) use ($fechaInicio, $fechaFin) {
            // Asumiendo que la tabla reservas utiliza created_at o campos de fecha para el periodo.
            // Si la columna en tu BD es 'fecha_reserva' o similar, debes ajustarla aquí.
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }])
            ->whereHas('empleado.usuario', function ($query) {
                $query->where('estado', 'ACTIVO');
            })
            ->get();
    }
}
