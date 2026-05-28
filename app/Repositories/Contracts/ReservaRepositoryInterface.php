<?php

namespace App\Repositories\Contracts;

use App\DTOs\ReservaCreateDTO;
use App\Models\Reserva;
use Illuminate\Database\Eloquent\Collection;

interface ReservaRepositoryInterface extends RepositoryInterface
{
    public function crear(ReservaCreateDTO $dto): Reserva;

    public function porCliente(int $cliente_id): Collection;

    public function hayDisponibilidad(string $fecha, string $hora): bool;

    /**
     * Retorna todas las reservas que coincidan con el estado dado.
     */
    public function porEstado(string $estado): Collection;

    /**
     * Retorna todas las reservas asignadas a un fotógrafo.
     */
    public function porFotografo(int $fotografoId): Collection;

    /**
     * Retorna todas las reservas cuya fecha_inicio coincida con la fecha dada (Y-m-d).
     */
    public function porFecha(string $fecha): Collection;
}
