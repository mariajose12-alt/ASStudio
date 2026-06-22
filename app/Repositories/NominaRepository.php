<?php

namespace App\Repositories;

use App\Models\Nomina;
use App\Repositories\Contracts\NominaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NominaRepository implements NominaRepositoryInterface
{
    public function __construct(protected Nomina $model) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['creadaPor.empleado.usuario.persona', 'detalles.fotografo.empleado.usuario.persona'])
            ->latest('fecha_inicio')
            ->get();
    }

    public function find(int $id): ?Nomina
    {
        return $this->model
            ->with(['creadaPor.empleado.usuario.persona', 'detalles.fotografo.empleado.usuario.persona'])
            ->find($id);
    }

    public function create(array $data): Nomina
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $nomina = $this->model->find($id);

        if (!$nomina) {
            return false;
        }

        return $nomina->update($data);
    }

    public function delete(int $id): bool
    {
        $nomina = $this->model->find($id);

        if (!$nomina) {
            return false;
        }

        return $nomina->delete();
    }

    public function porPeriodo(string $periodo): ?Nomina
    {
        return Nomina::where('periodo', $periodo)
            ->with(['creadaPor.empleado.usuario.persona', 'detalles.fotografo.empleado.usuario.persona'])
            ->first();
    }

    public function porRangoFechas(string $fechaInicio, string $fechaFin): Collection
    {
        return Nomina::where('fecha_inicio', '>=', $fechaInicio)
            ->where('fecha_fin', '<=', $fechaFin)
            ->with(['creadaPor.empleado.usuario.persona', 'detalles'])
            ->latest('fecha_inicio')
            ->get();
    }

    public function periodoYaProcesado(string $periodo): bool
    {
        return Nomina::where('periodo', $periodo)
            ->whereIn('estado', ['CALCULADA', 'PAGADA', 'CERRADA'])
            ->exists();
    }

    public function ultimaNomina(): ?Nomina
    {
        return Nomina::with(['creadaPor.empleado.usuario.persona', 'detalles'])
            ->latest('fecha_inicio')
            ->first();
    }
}
