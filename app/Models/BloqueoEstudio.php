<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoEstudio extends Model
{
    protected $table = 'bloqueos_estudio';
    protected $fillable = ['inicio', 'fin', 'motivo', 'creado_por_id'];
    protected $casts = ['inicio' => 'datetime', 'fin' => 'datetime'];

    public function creadoPor()
    {
        return $this->belongsTo(Usuario::class, 'creado_por_id');
    }

    public function scopeSolapaCon($query, $inicio, $fin)
    {
        return $query->where('inicio', '<', $fin)->where('fin', '>', $inicio);
    }
}
