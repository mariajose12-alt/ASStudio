<?php

namespace App\Repositories;

use App\Models\Notificacion;
use App\Repositories\Contracts\NotificacionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class NotificacionRepository implements NotificacionRepositoryInterface
{
    public function __construct(protected Notificacion $model) {}

    public function all(): Collection
    {
        return $this->model->with('usuario')->latest('fecha_envio')->get();
    }

    public function find(int $id): ?Notificacion
    {
        return $this->model->with('usuario')->find($id);
    }

    public function create(array $data): Notificacion
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $notificacion = $this->model->find($id);

        if (!$notificacion) {
            return false;
        }

        return $notificacion->update($data);
    }

    public function delete(int $id): bool
    {
        $notificacion = $this->model->find($id);

        if (!$notificacion) {
            return false;
        }

        return $notificacion->delete();
    }

    public function porUsuario(int $usuarioId): Collection
    {
        return Notificacion::where('usuario_id', $usuarioId)
            ->latest('fecha_envio')
            ->get();
    }

    public function noLeidasPorUsuario(int $usuarioId): Collection
    {
        return Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->latest('fecha_envio')
            ->get();
    }

    public function porTipo(int $usuarioId, string $tipo): Collection
    {
        return Notificacion::where('usuario_id', $usuarioId)
            ->where('tipo', $tipo)
            ->latest('fecha_envio')
            ->get();
    }

    public function marcarLeida(int $notificacionId): bool
    {
        return Notificacion::where('id', $notificacionId)
                ->update(['leida' => true]) > 0;
    }

    public function marcarTodasLeidas(int $usuarioId): bool
    {
        Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->update(['leida' => true]);

        return true;
    }

    public function contarNoLeidas(int $usuarioId): int
    {
        return Notificacion::where('usuario_id', $usuarioId)
            ->where('leida', false)
            ->count();
    }
}
