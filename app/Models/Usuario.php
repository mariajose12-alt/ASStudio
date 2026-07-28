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

    /**
     * Establece el estado del usuario (ACTIVO/INACTIVO). Fuera de
     * $fillable a propósito: nunca debe poder setearse vía asignación
     * masiva desde un request.
     */
    public function establecerEstado(string $estado): void
    {
        $this->estado = $estado;
        $this->save();
    }

    /**
     * Marca al usuario como verificado y limpia el token pendiente.
     */
    public function marcarVerificado(): void
    {
        $this->verificado = true;
        $this->token_verificacion = null;
        $this->save();
    }

    /**
     * Asigna (o renueva) el token de verificación pendiente.
     */
    public function asignarTokenVerificacion(string $token): void
    {
        $this->token_verificacion = $token;
        $this->save();
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
        return $this->empleado?->esFotografo() ?? false;
    }

    public function esAdministrador(): bool
    {
        return $this->empleado?->esAdministrador() ?? false;
    }

    public function getRol(): string
    {
        if ($this->esAdministrador())  return 'ADMINISTRADOR';
        if ($this->esFotografo())      return 'FOTOGRAFO';
        if ($this->esSocioEstudio())   return 'SOCIO_ESTUDIO';
        if ($this->esCliente())        return 'CLIENTE';
        return 'DESCONOCIDO';
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->esAdministrador())  $roles[] = 'ADMINISTRADOR';
        if ($this->esFotografo())      $roles[] = 'FOTOGRAFO';
        if ($this->esSocioEstudio())   $roles[] = 'SOCIO_ESTUDIO';
        if ($this->esCliente())        $roles[] = 'CLIENTE';
        return $roles;
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
        return $query->whereHas('empleado.administrador');
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

    public function esSocioEstudio(): bool
    {
        return $this->empleado?->rol === 'SOCIO_ESTUDIO';
    }

    public function tieneMultiplesRoles(): bool
    {
        return count(array_intersect($this->getRoles(), ['ADMINISTRADOR', 'FOTOGRAFO'])) > 1;
    }
}
