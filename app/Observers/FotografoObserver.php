<?php

namespace App\Observers;

use App\Models\Fotografo;
use App\Models\HistorialCambioFotografo;

class FotografoObserver
{
    private const CAMPOS_AUDITABLES = ['salario_base', 'dependientes_adicionales'];

    public function updated(Fotografo $fotografo): void
    {
        $administradorId = auth()->check() && auth()->user()->empleado?->administrador
            ? auth()->user()->empleado->administrador->id
            : null;

        foreach (self::CAMPOS_AUDITABLES as $campo) {
            if (! $fotografo->wasChanged($campo)) {
                continue;
            }

            HistorialCambioFotografo::create([
                'fotografo_id'     => $fotografo->id,
                'campo'            => $campo,
                'valor_anterior'   => $fotografo->getOriginal($campo),
                'valor_nuevo'      => $fotografo->getAttribute($campo),
                'cambiado_por_id'  => $administradorId,
            ]);
        }
    }
}
