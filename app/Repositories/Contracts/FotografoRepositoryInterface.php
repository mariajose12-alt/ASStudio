<?php

namespace App\Repositories\Contracts;

use App\Models\Fotografo;
use Illuminate\Database\Eloquent\Collection;

interface FotografoRepositoryInterface extends RepositoryInterface
{
    /**
     * Obtiene todos los fotógrafos.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Encuentra un fotógrafo por su ID.
     *
     * @param int $id
     * @return Fotografo|null
     */
    public function find(int $id): ?Fotografo;

    /**
     * Crea un nuevo registro de fotógrafo.
     *
     * @param array $data
     * @return Fotografo
     */
    public function create(array $data): Fotografo;

    /**
     * Actualiza un fotógrafo existente.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Elimina un fotógrafo por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Obtiene todos los fotógrafos cuyo usuario asociado esté en estado ACTIVO.
     *
     * @return Collection
     */
    public function listarActivos(): Collection;

    /**
     * Obtiene los fotógrafos con sus reservas cargadas para un período específico.
     * Útil para calcular disponibilidad o carga laboral.
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return Collection
     */
    public function buscarConReservasEnPeriodo(string $fechaInicio, string $fechaFin): Collection;
}
