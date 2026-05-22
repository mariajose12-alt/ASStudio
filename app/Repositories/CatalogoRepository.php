<?php

namespace App\Repositories;

use App\Models\Catalogo;
use App\Repositories\Contracts\CatalogoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class CatalogoRepository implements CatalogoRepositoryInterface
{
    protected Catalogo $model;

    public function __construct(Catalogo $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        // Traemos el creador (con su persona) y los paquetes fotográficos asociados.
        return $this->model->with(['creador.persona', 'paquetes'])->get();
    }

    public function find(int $id): ?Catalogo
    {
        return $this->model->with(['creador.persona', 'paquetes'])->find($id);
    }

    public function create(array $data): Catalogo
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $catalogo = $this->model->find($id);

        if (!$catalogo) {
            return false;
        }

        return $catalogo->update($data);
    }

    public function delete(int $id): bool
    {
        $catalogo = $this->model->find($id);

        if (!$catalogo) {
            return false;
        }

        return $catalogo->delete();
    }

    public function listarActivos(): Collection
    {
        $hoy = Carbon::now()->toDateString();

        return $this->model->with(['creador.persona', 'paquetes'])
            ->where('activo', true)
            ->where(function ($query) use ($hoy) {
                // Inicio de vigencia es null (siempre inició) o es menor/igual a hoy
                $query->whereNull('fecha_inicio_vigencia')
                    ->orWhere('fecha_inicio_vigencia', '<=', $hoy);
            })
            ->where(function ($query) use ($hoy) {
                // Fin de vigencia es null (nunca caduca) o es mayor/igual a hoy
                $query->whereNull('fecha_fin_vigencia')
                    ->orWhere('fecha_fin_vigencia', '>=', $hoy);
            })
            ->get();
    }

    public function cambiarEstado(int $id, bool $estado): bool
    {
        $catalogo = $this->model->find($id);

        if (!$catalogo) {
            return false;
        }

        $catalogo->activo = $estado;
        return $catalogo->save();
    }
}
