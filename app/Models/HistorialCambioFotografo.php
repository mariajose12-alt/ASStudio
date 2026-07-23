<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialCambioFotografo extends Model
{
    protected $table = 'historial_cambios_fotografo';

    protected $fillable = [
        'fotografo_id',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'cambiado_por_id',
    ];

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    public function cambiadoPor(): BelongsTo
    {
        return $this->belongsTo(Administrador::class, 'cambiado_por_id');
    }

    public function etiquetaCampo(): string
    {
        return match ($this->campo) {
            'salario_base' => 'Salario base',
            'dependientes_adicionales' => 'Dependientes adicionales',
            default => $this->campo,
        };
    }
}
