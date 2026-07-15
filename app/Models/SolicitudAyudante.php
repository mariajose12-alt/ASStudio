<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SolicitudAyudante extends Model
{
    protected $table = 'solicitudes_ayudantes';

    protected $fillable = [
        'sesion_id',
        'fotografo_solicitante_id',
        'cantidad_ayudantes',
        'cupos_confirmados',
        'estado',
        'mensaje',
    ];

    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_solicitante_id');
    }

    public function postulaciones(): HasMany
    {
        return $this->hasMany(PostulacionAyudante::class, 'solicitud_ayudante_id');
    }

    public function postulacionesPendientes(): HasMany
    {
        return $this->postulaciones()->where('estado', 'PENDIENTE');
    }

    // Helpers
    public function estaAbierta(): bool
    {
        return $this->estado === 'ABIERTA';
    }

    public function cuposDisponibles(): int
    {
        return max(0, $this->cantidad_ayudantes - $this->cupos_confirmados);
    }

    public function tieneCupoDisponible(): bool
    {
        return $this->estaAbierta() && $this->cuposDisponibles() > 0;
    }
}
