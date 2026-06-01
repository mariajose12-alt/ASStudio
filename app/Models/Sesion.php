<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sesion extends Model
{
    protected $table = 'sesiones';

    protected $fillable = [
        'reserva_id',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    // Relaciones
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(ParticipacionSesion::class, 'sesion_id');
    }

    public function fotografos(): BelongsToMany
    {
        return $this->belongsToMany(Fotografo::class, 'participaciones_sesion', 'sesion_id', 'fotografo_id')
            ->withPivot(['rol', 'porcentaje_comision', 'horas_trabajadas', 'estado_participacion'])
            ->withTimestamps();
    }

    public function fotografias(): HasMany
    {
        return $this->hasMany(Fotografia::class, 'sesion_id');
    }

    // Helpers
    public function estaActiva(): bool
    {
        return in_array($this->estado, ['CONFIRMADA', 'EN_PROCESO', 'EN_EDICION']);
    }

    public function estaFinalizada(): bool
    {
        return in_array($this->estado, ['FINALIZADA', 'CERRADA']);
    }

}
