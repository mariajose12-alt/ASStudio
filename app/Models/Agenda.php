<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    public function fotografo()
    {
        return $this->belongsTo(Fotografo::class);
    }

    //Scopes — filtros reutilizables

    // Agenda::disponible()->get()
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    // Agenda::enFecha('2025-06-10')->get()
    public function scopeEnFecha($query, string $fecha)
    {
        return $query->whereDate('fecha_inicio', $fecha);
    }

    // Agenda::queContiene('2025-06-10', '09:00')->get()
    public function scopeQueContiene($query, string $fecha, string $hora)
    {
        $fechaHora = Carbon::parse("$fecha $hora");

        return $query
            ->where('fecha_inicio', '<=', $fechaHora)
            ->where('fecha_fin',    '>=', $fechaHora);
    }

    // Helpers

    // $agenda->cubre('2025-06-10', '09:00')
    public function cubre(string $fecha, string $hora): bool
    {
        $fechaHora = Carbon::parse("$fecha $hora");

        return $this->fecha_inicio <= $fechaHora
            && $this->fecha_fin    >= $fechaHora;
    }
}
