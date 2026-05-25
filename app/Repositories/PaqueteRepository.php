<?php

namespace App\Repositories;

use App\Models\PaqueteFotografico;
use App\Repositories\Contracts\PaqueteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaqueteRepository implements PaqueteRepositoryInterface
{
    protected PaqueteFotografico $model;

    public function __construct(PaqueteFotografico $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('catalogos')->get();
    }

    public function find(int $id): ?PaqueteFotografico
    {
        return $this->model->with('catalogos')->find($id);
    }

    public function create(array $data): PaqueteFotografico
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $paquete = $this->model->find($id);

        if (!$paquete) {
            return false;
        }

        return $paquete->update($data);
    }

    public function delete(int $id): bool
    {
        $paquete = $this->model->find($id);

        if (!$paquete) {
            return false;
        }

        return $paquete->delete();
    }

    public function listarActivos(): Collection
    {
        return $this->model->with('catalogos')
            ->where('activo', true)
            ->get();
    }

    public function porCatalogo(int $catalogoId): Collection
    {
        return $this->model->with('catalogos')
            ->whereHas('catalogos', fn($q) => $q->where('catalogos.id', $catalogoId))
            ->get();
    }

    public function limiteFotos(int $paqueteId): int
    {
        $paquete = $this->model->find($paqueteId);

        return $paquete ? (int) $paquete->cantidad_fotos_incluidas : 0;
    }
}
