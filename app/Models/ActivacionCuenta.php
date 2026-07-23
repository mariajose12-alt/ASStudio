<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ActivacionCuenta extends Model
{
    protected $table = 'activaciones_cuenta';

    protected $fillable = ['usuario_id', 'token', 'expira_en', 'usado_en'];

    protected $casts = [
        'expira_en' => 'datetime',
        'usado_en'  => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Crea un token de activación y devuelve el token EN CRUDO
     * (para meterlo en la URL del correo). Solo se guarda su hash.
     */
    public static function generarPara(Usuario $usuario): string
    {
        // Invalida tokens anteriores no usados de este usuario
        static::where('usuario_id', $usuario->id)
            ->whereNull('usado_en')
            ->delete();

        $tokenCrudo = Str::random(64);

        static::create([
            'usuario_id' => $usuario->id,
            'token'      => Hash::make($tokenCrudo),
            'expira_en'  => Carbon::now()->addHours(48),
        ]);

        return $tokenCrudo;
    }

    public function esValido(): bool
    {
        return is_null($this->usado_en) && $this->expira_en->isFuture();
    }
}
