<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
        'usuario_id',
    ];

    //  Relaciones
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'cliente_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'cliente_id');
    }

    //  Helpers
    public function persona(): Persona
    {
        return $this->usuario->persona;
    }
}
