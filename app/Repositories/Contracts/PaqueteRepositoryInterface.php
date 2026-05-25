<?php

namespace App\Repositories\Contracts;

use App\Models\PaqueteFotografico;
use Illuminate\Database\Eloquent\Collection;

interface PaqueteRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?PaqueteFotografico;

    public function create(array $data): PaqueteFotografico;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    /**
     * Retorna todos los paquetes con activo = true.
     */
    public function listarActivos(): Collection;

    /**
     * Retorna los paquetes asociados a un catálogo específico.
     */
    public function porCatalogo(int $catalogoId): Collection;

    /**
     * Retorna la cantidad de fotos incluidas en un paquete.
     * Devuelve 0 si el paquete no existe.
     */
    public function limiteFotos(int $paqueteId): int;
}
