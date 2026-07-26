<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudEstudio extends Model
{
    protected $table = 'solicitudes_estudio';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'invitados',
        'finalidad',
        'cantidad_personas',
        'color_fondo_adicional',
        'color_fondo',
        'iluminacion',
        'iluminacion_otro',
        'fecha',
        'hora_inicio',
        'hora_fin',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'color_fondo_adicional' => 'boolean',
        'cantidad_personas' => 'integer',
        'aprobada_at' => 'datetime',
    ];

    public function aprobadaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'aprobada_por');
    }

    // Scopes útiles para el Service (evita repetir where() sueltos)
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeEnFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }
}
