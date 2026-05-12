<?php

namespace App\Repositories;

use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    protected Usuario $model;

    public function __construct(Usuario $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        // Traemos también la relación persona por defecto para evitar N+1 queries
        return $this->model->with('persona')->get();
    }

    public function find(int $id): ?Usuario
    {
        return $this->model->with('persona')->find($id);
    }

    public function create(array $data): Usuario
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $usuario = $this->model->find($id);

        if (!$usuario) {
            return false;
        }

        return $usuario->update($data);
    }

    public function delete(int $id): bool
    {
        $usuario = $this->model->find($id);

        if (!$usuario) {
            return false;
        }

        return $usuario->delete();
    }

    public function findByEmail(string $email): ?Usuario
    {
        return $this->model->where('email', $email)->with('persona')->first();
    }

    public function findByRol(string $rol): Collection
    {
        // En ASStudio, la asignación de roles depende de tablas polimórficas/relacionadas.
        // Si buscamos clientes, consultamos la relación cliente.
        if (strtoupper($rol) === 'CLIENTE') {
            return $this->model->has('cliente')->with('persona')->get();
        }

        // Si buscamos FOTOGRAFO o ADMINISTRADOR, consultamos la relación empleado.
        return $this->model->whereHas('empleado', function ($query) use ($rol) {
            $query->where('rol', strtoupper($rol));
        })->with('persona')->get();
    }
}
