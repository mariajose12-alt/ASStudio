<?php

namespace App\Repositories\Contracts;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

interface UsuarioRepositoryInterface extends RepositoryInterface
{
    /**
     * Obtiene todos los usuarios.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Encuentra un usuario por su ID.
     *
     * @param int $id
     * @return Usuario|null
     */
    public function find(int $id): ?Usuario;

    /**
     * Crea un nuevo usuario.
     *
     * @param array $data
     * @return Usuario
     */
    public function create(array $data): Usuario;

    /**
     * Actualiza un usuario existente.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Encuentra un usuario por su correo electrónico.
     *
     * @param string $email
     * @return Usuario|null
     */
    public function findByEmail(string $email): ?Usuario;

    /**
     * Encuentra todos los usuarios que pertenecen a un rol específico.
     *
     * @param string $rol 'ADMINISTRADOR', 'FOTOGRAFO' o 'CLIENTE'
     * @return Collection
     */
    public function findByRol(string $rol): Collection;
}
