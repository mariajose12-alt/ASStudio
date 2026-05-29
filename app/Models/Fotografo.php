<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fotografo extends Model
{
    protected $table = 'fotografos';

    protected $fillable = [
        'empleado_id',
        'certificaciones',
        'experiencia_laboral',
    ];

    protected $casts = [
        'certificaciones' => 'array', // guardado como JSON
    ];

    //  Relaciones
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function agenda(): HasMany
    {
        return $this->hasMany(Agenda::class, 'fotografo_id');
    }

    public function participaciones(): HasMany
    {
        return $this->hasMany(ParticipacionSesion::class, 'fotografo_id');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioFotografo::class);
    }

    public function detallesNomina(): HasMany
    {
        return $this->hasMany(DetalleNomina::class, 'fotografo_id');
    }

    // Reservas en las que participó como asistente/principal
    public function sesiones()
    {
        return $this->belongsToMany(Sesion::class, 'participaciones_sesion', 'fotografo_id', 'sesion_id')
            ->withPivot(['rol', 'porcentaje_comision', 'horas_trabajadas', 'estado_participacion'])
            ->withTimestamps();
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

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'fotografo_id');
    }
}
