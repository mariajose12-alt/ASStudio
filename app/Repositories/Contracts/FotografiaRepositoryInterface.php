<?php

namespace App\Repositories\Contracts;

use App\Models\Fotografia;
use Illuminate\Database\Eloquent\Collection;

interface FotografiaRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Fotografia;

    public function create(array $data): Fotografia;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function porSesion(int $sesionId): Collection;

    public function seleccionadas(int $sesionId): Collection;

    public function porEstado(int $sesionId, string $estado): Collection;

    public function marcarSeleccionada(int $fotografiaId, bool $seleccionada): bool;

    public function urlTemporal(Fotografia $fotografia, int $minutos = 60): string;
}
