<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Fotografia extends Model
{
    protected $fillable = [
        'sesion_id',
        'subido_por_fotografo_id',
        'url',
        'url_thumb',
        'nombre_original',
        'fecha_captura',
        'estado',
        'seleccionada',
        'aprobada',
    ];

    protected $casts = [
        'fecha_captura' => 'datetime',
        'seleccionada'  => 'boolean',
        'aprobada'      => 'boolean',
        'estado'        => 'string',
    ];

    // Ocultar la URL interna de R2, siempre exponer la firmada
    protected $hidden = ['url', 'url_thumb'];

    // Agregar la URL firmada automáticamente en cada respuesta JSON
    protected $appends = ['url_firmada', 'url_thumb_firmada'];

    // Relación con Sesion
    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    public function subidaPor(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'subido_por_fotografo_id');
    }

    // Accessor — genera la URL firmada al vuelo, cacheada por foto
    // para evitar una llamada nueva a R2 cada vez que se serializa
    // o se accede a esta propiedad (ver L-4 de la auditoría).
    public function getUrlFirmadaAttribute(): string
    {
        return Cache::remember(
            "foto_url_firmada_{$this->id}",
            now()->addMinutes(50), // un poco menos que los 60 min de expiración real
            fn () => Storage::disk('r2')->temporaryUrl($this->url, now()->addMinutes(60))
        );
    }

    public function getUrlThumbFirmadaAttribute(): ?string
    {
        if (! $this->url_thumb) {
            return null; // ej. fue un RAW, no tiene thumbnail
        }

        return Cache::remember(
            "foto_thumb_firmada_{$this->id}",
            now()->addMinutes(50),
            fn () => Storage::disk('r2')->temporaryUrl($this->url_thumb, now()->addMinutes(60))
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

    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }

    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false);
    }
}
