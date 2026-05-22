<?php

namespace App\Repositories\Contracts;

use App\Models\Catalogo;
use Illuminate\Database\Eloquent\Collection;

interface CatalogoRepositoryInterface
{
    /**
     * Obtiene todos los catálogos, sin importar su estado.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Encuentra un catálogo por su ID.
     *
     * @param int $id
     * @return Catalogo|null
     */
    public function find(int $id): ?Catalogo;

    /**
     * Crea un nuevo catálogo.
     *
     * @param array $data
     * @return Catalogo
     */
    public function create(array $data): Catalogo;

    /**
     * Actualiza un catálogo existente.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Elimina un catálogo por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Obtiene todos los catálogos que están activos Y dentro de su periodo de vigencia.
     * Ideal para mostrar a los clientes.
     *
     * @return Collection
     */
    public function listarActivos(): Collection;

    /**
     * Cambia rápidamente el estado (activo/inactivo) de un catálogo.
     *
     * @param int $id
     * @param bool $estado
     * @return bool
     */
    public function cambiarEstado(int $id, bool $estado): bool;
}
