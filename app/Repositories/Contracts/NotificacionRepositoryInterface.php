<?php

namespace App\Repositories\Contracts;

use App\Models\Notificacion;
use Illuminate\Database\Eloquent\Collection;

interface NotificacionRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Notificacion;

    public function create(array $data): Notificacion;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function porUsuario(int $usuarioId): Collection;

    public function noLeidasPorUsuario(int $usuarioId): Collection;

    public function porTipo(int $usuarioId, string $tipo): Collection;

    public function marcarLeida(int $notificacionId): bool;

    public function marcarTodasLeidas(int $usuarioId): bool;

    public function contarNoLeidas(int $usuarioId): int;
}
