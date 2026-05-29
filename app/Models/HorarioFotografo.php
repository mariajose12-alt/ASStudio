<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioFotografo extends Model
{
    protected $table = 'horarios_fotografo';

    protected $fillable = [
        'fotografo_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
    ];

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class);
    }

    /**
     * Nombres de los días para legibilidad en logs/debug.
     * 0=Domingo, 1=Lunes, ..., 6=Sábado
     */
    public function getNombreDiaAttribute(): string
    {
        return ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][$this->dia_semana];
    }

}
