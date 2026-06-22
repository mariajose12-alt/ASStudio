<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Comprobante extends Model
{
    protected $table = 'comprobantes';

    protected $fillable = [
        'url_archivo',
        'nombre_archivo',
        'fecha_subida',
        'validado',
    ];

    protected $casts = [
        'fecha_subida' => 'datetime',
        'validado'     => 'boolean',
    ];

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class, 'comprobante_id');
    }

    public function urlFirmada(int $minutos = 60): string
    {
        return Storage::disk('r2')->temporaryUrl($this->url_archivo, now()->addMinutes($minutos));
    }
}
