<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'paquete_id',
        'catalogo_id',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'descripcion',
        'tipo',
        'precio_total',
        'estado',
        'fotografo_id',
        'motivo_rechazo',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function paquete()
    {
        return $this->belongsTo(PaqueteFotografico::class, 'paquete_id');
    }

    public function fotografo()
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }
}
