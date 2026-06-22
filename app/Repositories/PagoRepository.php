<?php

namespace App\Repositories;

use App\Models\Pago;
use App\Repositories\Contracts\PagoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PagoRepository implements PagoRepositoryInterface
{
    public function __construct(protected Pago $model) {}

    public function all(): Collection
    {
        return $this->model
            ->with(['reserva', 'cliente.usuario.persona', 'comprobante'])
            ->latest('fecha_registro')
            ->get();
    }

    public function find(int $id): ?Pago
    {
        return $this->model
            ->with(['reserva', 'cliente.usuario.persona', 'comprobante'])
            ->find($id);
    }

    public function create(array $data): Pago
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pago = $this->model->find($id);

        if (!$pago) {
            return false;
        }

        return $pago->update($data);
    }

    public function delete(int $id): bool
    {
        $pago = $this->model->find($id);

        if (!$pago) {
            return false;
        }

        return $pago->delete();
    }

    public function porReserva(int $reservaId): Collection
    {
        return Pago::where('reserva_id', $reservaId)
            ->with(['comprobante'])
            ->latest('fecha_registro')
            ->get();
    }

    public function porTipo(string $tipo): Collection
    {
        return Pago::where('tipo', $tipo)
            ->with(['reserva', 'cliente.usuario.persona', 'comprobante'])
            ->latest('fecha_registro')
            ->get();
    }

    public function porEstado(string $estado): Collection
    {
        return Pago::where('estado', $estado)
            ->with(['reserva', 'cliente.usuario.persona', 'comprobante'])
            ->latest('fecha_registro')
            ->get();
    }

    public function porReservaYTipo(int $reservaId, string $tipo): ?Pago
    {
        return Pago::where('reserva_id', $reservaId)
            ->where('tipo', $tipo)
            ->with(['comprobante'])
            ->first();
    }

    public function totalConfirmadoPorReserva(int $reservaId): float
    {
        return (float) Pago::where('reserva_id', $reservaId)
            ->where('estado', 'CONFIRMADO')
            ->sum('monto');
    }
}
