<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'usuario_id',
        'rol',
    ];

    //  Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function fotografo(): HasOne
    {
        return $this->hasOne(Fotografo::class, 'empleado_id');
    }

    public function administrador(): HasOne
    {
        return $this->hasOne(Administrador::class, 'empleado_id');
    }

    //  Helpers
    public function esFotografo(): bool
    {
        return $this->rol === 'FOTOGRAFO';
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'ADMINISTRADOR';
    }
}
