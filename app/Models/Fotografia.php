<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Fotografia extends Model
{
    protected $fillable = [
        'sesion_id',
        'subido_por_fotografo_id',
        'url',
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
    protected $hidden = ['url'];

    // Agregar la URL firmada automáticamente en cada respuesta JSON
    protected $appends = ['url_firmada'];

    // Relación con Sesion
    public function sesion(): BelongsTo
    {
        return $this->belongsTo(Sesion::class);
    }

    public function subidaPor(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'subido_por_fotografo_id');
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

    public function scopeAprobadas($query)
    {
        return $query->where('aprobada', true);
    }

    public function scopePendientesAprobacion($query)
    {
        return $query->where('aprobada', false);
    }
}
