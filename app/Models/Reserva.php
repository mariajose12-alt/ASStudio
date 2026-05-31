<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'paquete_id',
        'catalogo_id',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'descripcion',
        'tipo',
        'precio_total',
        'estado',
        'fotografo_id',
        'motivo_rechazo',
        'duracion_horas'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(PaqueteFotografico::class, 'paquete_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    public function sesion(): HasOne
    {
        return $this->hasOne(Sesion::class, 'reserva_id');
    }
}
