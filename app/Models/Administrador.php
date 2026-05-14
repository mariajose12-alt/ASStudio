<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrador extends Model
{
    protected $table = 'administradores';

    protected $fillable = [
        'empleado_id',
    ];

    //  Relaciones
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function nominas(): HasMany
    {
        return $this->hasMany(Nomina::class, 'creada_por_id');
    }

    //  Helpers

    public function getUsuario(): Usuario
    {
        return $this->empleado->usuario;
    }

    public function getPersona(): Persona
    {
        return $this->empleado->usuario->persona;
    }
}
