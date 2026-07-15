<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostulacionAyudante extends Model
{
    protected $table = 'postulaciones_ayudante';

    protected $fillable = [
        'solicitud_ayudante_id',
        'fotografo_id',
        'estado',
    ];

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(SolicitudAyudante::class, 'solicitud_ayudante_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    public function esPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    public function esConfirmado(): bool
    {
        return $this->estado === 'CONFIRMADO';
    }
}
