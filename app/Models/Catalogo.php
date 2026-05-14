<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Catalogo extends Model
{
    protected $table = 'catalogos';

    protected $fillable = [
        'creador_id',
        'nombre',
        'descripcion',
        'activo',
        'fecha_inicio_vigencia',
        'fecha_fin_vigencia',
    ];

    protected $casts = [
        'activo'                => 'boolean',
        'fecha_inicio_vigencia' => 'date',
        'fecha_fin_vigencia'    => 'date',
    ];

    public function creador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creador_id');
    }

    public function paquetes(): BelongsToMany
    {
        return $this->belongsToMany(
            PaqueteFotografico::class,
            'catalogo_paquete',
            'catalogo_id',
            'paquete_id'
        );
    }

    // Helper: saber si el catálogo está vigente hoy
    public function estaVigente(): bool
    {
        $hoy = now()->toDateString();

        $inicioOk = is_null($this->fecha_inicio_vigencia) || $this->fecha_inicio_vigencia->lte(now());
        $finOk    = is_null($this->fecha_fin_vigencia)    || $this->fecha_fin_vigencia->gte(now());

        return $this->activo && $inicioOk && $finOk;
    }
}
