<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    protected $fillable = [
        'fotografo_id',
        'fecha_inicio',
        'fecha_fin',
        'disponible',
        'descripcion',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
        'disponible'   => 'boolean',
    ];

    // Relaciones

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    //Scopes — filtros reutilizables

    // Agenda::disponible()->get()
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    // Agenda::enFecha('2026-06-10')->get()
    public function scopeEnFecha($query, string $fecha)
    {
        return $query->whereDate('fecha_inicio', $fecha);
    }

    // Busca slots que cubran completamente el rango dado
    // Agenda::queCubreRango('2026-06-10 09:00', '2026-06-10 12:00')->get()
    public function scopeQueCubreRango($query, string $inicio, string $fin)
    {
        return $query
            ->where('fecha_inicio', '<=', Carbon::parse($inicio))
            ->where('fecha_fin',    '>=', Carbon::parse($fin));
    }


    // Helpers

    // $agenda->cubre('2026-06-10', '09:00')
    public function cubre(string $fecha, string $hora): bool
    {
        $fechaHora = Carbon::parse("$fecha $hora");

        return $this->fecha_inicio <= $fechaHora
            && $this->fecha_fin    >= $fechaHora;
    }

    // Marca el slot como ocupado y lo persiste
    // $agenda->ocupar()
    public function ocupar(): bool
    {
        $this->disponible = false;
        return $this->save();
    }

    // Marca el slot como libre nuevamente (ej: cancelación)
    // $agenda->liberar()
    public function liberar(): bool
    {
        $this->disponible = true;
        return $this->save();
    }

}
