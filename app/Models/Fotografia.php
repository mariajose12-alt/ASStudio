<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Fotografia extends Model
{
    protected $fillable = [
        'sesion_id',
        'url',
        'fecha_captura',
        'estado',
        'seleccionada',
    ];

    protected $casts = [
        'fecha_captura' => 'datetime',
        'seleccionada'  => 'boolean',
        'estado'        => 'string',
    ];

    // Ocultar la URL interna de R2, siempre exponer la firmada
    protected $hidden = ['url'];

    // Agregar la URL firmada automáticamente en cada respuesta JSON
    protected $appends = ['url_firmada'];

    // Relación con Sesion
    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    // Accessor — genera la URL firmada al vuelo
    public function getUrlFirmadaAttribute(): string
    {
        return Storage::disk('r2')->temporaryUrl(
            $this->url,
            now()->addMinutes(60)
        );
    }

    // Scopes útiles
    public function scopeOriginales($query)
    {
        return $query->where('estado', 'ORIGINAL');
    }

    public function scopeEditadas($query)
    {
        return $query->where('estado', 'EDITADA');
    }

    public function scopeSeleccionadas($query)
    {
        return $query->where('seleccionada', true);
    }
}
