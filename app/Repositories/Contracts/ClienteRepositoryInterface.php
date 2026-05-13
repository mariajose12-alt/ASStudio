<?php

namespace App\Repositories\Contracts;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Collection;

interface ClienteRepositoryInterface
{
    /**
     * Obtiene todos los clientes.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Encuentra un cliente por su ID.
     *
     * @param int $id
     * @return Cliente|null
     */
    public function find(int $id): ?Cliente;

    /**
     * Crea un nuevo cliente.
     *
     * @param array $data
     * @return Cliente
     */
    public function create(array $data): Cliente;

    /**
     * Actualiza un cliente existente.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Elimina un cliente por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Encuentra un cliente basándose en el ID de usuario.
     * Útil para cuando el usuario está logueado y necesitamos su perfil de cliente.
     *
     * @param int $usuarioId
     * @return Cliente|null
     */
    public function findByUsuarioId(int $usuarioId): ?Cliente;
}
