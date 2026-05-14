<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipacionSesion extends Model
{
    protected $table = 'participaciones_sesion';

    protected $fillable = [
        'sesion_id',
        'fotografo_id',
        'rol',
        'porcentaje_comision',
        'estado_participacion',
        'horas_trabajadas',
    ];

    protected $casts = [
        'estado_participacion' => 'boolean',
        'porcentaje_comision'  => 'decimal:2',
        'horas_trabajadas'     => 'decimal:2',
    ];

    // Relaciones
    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    // Helpers
    public function esPrincipal(): bool
    {
        return $this->rol === 'PRINCIPAL';
    }

    public function esAsistente(): bool
    {
        return $this->rol === 'ASISTENTE';
    }

    public function estaActiva(): bool
    {
        return $this->estado_participacion === true;
    }
}
