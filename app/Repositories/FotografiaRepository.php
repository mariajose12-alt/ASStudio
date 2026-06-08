<?php

namespace App\Repositories;

use App\Models\Fotografia;
use App\Repositories\Contracts\FotografiaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class FotografiaRepository implements FotografiaRepositoryInterface
{
    public function __construct(protected Fotografia $model) {}

    public function all(): Collection
    {
        return $this->model->with('sesion')->latest('fecha_captura')->get();
    }

    public function find(int $id): ?Fotografia
    {
        return $this->model->with('sesion')->find($id);
    }

    public function create(array $data): Fotografia
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $fotografia = $this->model->find($id);

        if (!$fotografia) {
            return false;
        }

        return $fotografia->update($data);
    }

    public function delete(int $id): bool
    {
        $fotografia = $this->model->find($id);

        if (!$fotografia) {
            return false;
        }

        Storage::disk('r2')->delete($fotografia->url);

        return $fotografia->delete();
    }

    public function porSesion(int $sesionId): Collection
    {
        return Fotografia::where('sesion_id', $sesionId)
            ->orderBy('fecha_captura')
            ->get();
    }

    public function seleccionadas(int $sesionId): Collection
    {
        return Fotografia::where('sesion_id', $sesionId)
            ->seleccionadas()
            ->orderBy('fecha_captura')
            ->get();
    }

    public function porEstado(int $sesionId, string $estado): Collection
    {
        return Fotografia::where('sesion_id', $sesionId)
            ->where('estado', $estado)
            ->orderBy('fecha_captura')
            ->get();
    }

    public function marcarSeleccionada(int $fotografiaId, bool $seleccionada): bool
    {
        return Fotografia::where('id', $fotografiaId)
                ->update(['seleccionada' => $seleccionada]) > 0;
    }

    public function urlTemporal(Fotografia $fotografia, int $minutos = 60): string
    {
        return Storage::disk('r2')->temporaryUrl(
            $fotografia->url,
            now()->addMinutes($minutos)
        );
    }
}
