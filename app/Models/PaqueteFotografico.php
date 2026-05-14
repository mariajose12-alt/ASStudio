<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaqueteFotografico extends Model
{
    protected $table = 'paquetes_fotograficos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_base',
        'cantidad_fotos_incluidas',
        'activo',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'precio_base' => 'decimal:2',
    ];

    public function catalogos(): BelongsToMany
    {
        return $this->belongsToMany(
            Catalogo::class,
            'catalogo_paquete',
            'paquete_id',
            'catalogo_id'
        );
    }
}
