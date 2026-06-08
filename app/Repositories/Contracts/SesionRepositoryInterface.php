<?php

namespace App\Repositories\Contracts;

use App\Models\Sesion;
use Illuminate\Database\Eloquent\Collection;

interface SesionRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Sesion;

    public function create(array $data): Sesion;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function porEstado(string $estado): Collection;

    public function porFotografo(int $fotografoId): Collection;

    public function porReserva(int $reservaId): ?Sesion;

    public function galeriaDisponible(): Collection;
}
