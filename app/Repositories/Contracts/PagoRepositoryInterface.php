<?php

namespace App\Repositories\Contracts;

use App\Models\Pago;
use Illuminate\Database\Eloquent\Collection;

interface PagoRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Pago;

    public function create(array $data): Pago;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function porReserva(int $reservaId): Collection;

    public function porTipo(string $tipo): Collection;

    public function porEstado(string $estado): Collection;

    public function porReservaYTipo(int $reservaId, string $tipo): ?Pago;

    public function totalConfirmadoPorReserva(int $reservaId): float;
}
