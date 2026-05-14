<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'persona_id',
        'email',
        'contrasena',
        'estado',
    ];

    protected $hidden = [
        'contrasena',
    ];

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    //  Relaciones
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    public function empleado(): HasOne
    {
        return $this->hasOne(Empleado::class, 'usuario_id');
    }

//    public function notificaciones(): HasMany
//    {
//        return $this->hasMany(Notificacion::class, 'usuario_id');
//    }

    //  Helpers de rol
    public function esCliente(): bool
    {
        return $this->cliente()->exists();
    }

    public function esFotografo(): bool
    {
        return $this->empleado?->rol === 'FOTOGRAFO';
    }

    public function esAdministrador(): bool
    {
        return $this->empleado?->rol === 'ADMINISTRADOR';
    }

    public function getRol(): string
    {
        if ($this->esAdministrador()) return 'ADMINISTRADOR';
        if ($this->esFotografo())     return 'FOTOGRAFO';
        if ($this->esCliente())       return 'CLIENTE';
        return 'DESCONOCIDO';
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'ACTIVO';
    }

    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }
}
