<?php

namespace App\Repositories\Contracts;

use App\Models\Nomina;
use Illuminate\Database\Eloquent\Collection;

interface NominaRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Nomina;

    public function create(array $data): Nomina;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function porPeriodo(string $periodo): ?Nomina;

    public function porRangoFechas(string $fechaInicio, string $fechaFin): Collection;

    public function periodoYaProcesado(string $periodo): bool;

    public function ultimaNomina(): ?Nomina;
}
