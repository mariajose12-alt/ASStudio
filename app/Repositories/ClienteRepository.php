<?php

namespace App\Repositories;

use App\Models\Cliente;
use App\Repositories\Contracts\ClienteRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ClienteRepository implements ClienteRepositoryInterface
{
    protected Cliente $model;

    public function __construct(Cliente $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        // Traemos en cascada la relación usuario y su respectiva persona
        return $this->model->with(['usuario.persona'])->get();
    }

    public function find(int $id): ?Cliente
    {
        return $this->model->with(['usuario.persona'])->find($id);
    }

    public function create(array $data): Cliente
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $cliente = $this->model->find($id);

        if (!$cliente) {
            return false;
        }

        return $cliente->update($data);
    }

    public function delete(int $id): bool
    {
        $cliente = $this->model->find($id);

        if (!$cliente) {
            return false;
        }

        return $cliente->delete();
    }

    public function findByUsuarioId(int $usuarioId): ?Cliente
    {
        return $this->model->with(['usuario.persona'])
            ->where('usuario_id', $usuarioId)
            ->first();
    }
}
