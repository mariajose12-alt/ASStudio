<?php

namespace App\Repositories\Contracts;

use App\DTOs\ReservaCreateDTO;
use App\Models\Reserva;

interface ReservaRepositoryInterface extends RepositoryInterface
{
    public function crear(ReservaCreateDTO $dto): Reserva;
    public function porCliente(int $cliente_id);
    public function hayDisponibilidad(string $fecha, string $hora): bool;
}
