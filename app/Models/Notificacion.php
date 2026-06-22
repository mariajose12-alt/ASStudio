<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'mensaje',
        'fecha_envio',
        'leida',
        'tipo',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'leida'       => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function estaLeida(): bool
    {
        return $this->leida;
    }
}
