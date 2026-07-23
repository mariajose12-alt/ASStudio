<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'google_id',
        'avatar',
        'estado',
        'verificado',
        'token_verificacion',
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'verificado' => 'boolean',
    ];

    protected $appends = [
        'tiene_google_vinculado'
    ];

    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

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

    public function scopeAdministradores($query)
    {
        return $query->whereHas('empleado', fn ($q) => $q->where('rol', 'ADMINISTRADOR'));
    }

    public function estaVerificado(): bool
    {
        return $this->verificado === true;
    }

    public function activacionesCuenta(): HasMany
    {
        return $this->hasMany(ActivacionCuenta::class, 'usuario_id');
    }

    protected function tieneGoogleVinculado(): Attribute
    {
        return Attribute::get(fn () => !is_null($this->google_id));
    }

}
