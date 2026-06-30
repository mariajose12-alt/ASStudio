<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaBanco extends Model
{
    protected $table = 'cuentas_banco';
    protected $fillable = ['titular', 'banco', 'numero_cuenta', 'tipo', 'moneda', 'activa', 'orden'];
    protected $casts = ['activa' => 'boolean'];

    public function scopeActivas($query)
    {
        return $query->where('activa', true)->orderBy('orden');
    }
}
