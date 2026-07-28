<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'cliente_id',
        'paquete_id',
        'catalogo_id',
        'fotografo_id',
        'fecha_inicio',
        'fecha_fin',
        'fecha_solicitud',
        'lugar',
        'descripcion',
        'tipo',
        'estado',
        'precio_total',
        'motivo_rechazo',
        'duracion_horas',
        'duracion_horas_propuesta',
    ];

    protected $casts = [
        'fecha_inicio'    => 'datetime',
        'fecha_fin'       => 'datetime',
        'fecha_solicitud' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(PaqueteFotografico::class, 'paquete_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'catalogo_id');
    }

    public function sesion(): HasOne
    {
        return $this->hasOne(Sesion::class, 'reserva_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }


    /**
     * Pago vigente de tipo ANTICIPO/COMPLETO (el que activa la sesión al confirmarse).
     */
    public function pagoAnticipo(): HasOne
    {
        return $this->hasOne(Pago::class)
            ->whereIn('tipo', ['ANTICIPO', 'COMPLETO'])
            ->latestOfMany();
    }

    public function pagoFinal(): HasOne
    {
        return $this->hasOne(Pago::class)->where('tipo', 'FINAL')->latestOfMany();
    }

    /* ── Helpers de estado de la reserva ── */

    public function estaAprobada(): bool
    {
        return $this->estado === 'APROBADA';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    /* ── Acciones del state machine de la reserva ── */

    /**
     * Aprueba la solicitud de reserva (acción del fotógrafo/admin).
     * No crea la sesión todavía — eso ocurre cuando se confirma el pago.
     */
    public function aprobar(): void
    {
        $this->estado = 'APROBADA';
        $this->save();
    }

    public function rechazar(): void
    {
        $this->estado = 'RECHAZADA';
        $this->save();
    }

    public function cancelar(): void
    {
        $this->estado = 'CANCELADA';
        $this->save();
    }


    public function reenviarParaRevision(string $descripcion, ?string $nuevaFecha = null, ?string $nuevaHora = null): void
    {
        $this->descripcion = $descripcion;
        $this->motivo_rechazo = null;
        $this->duracion_horas_propuesta = null;

        if ($nuevaFecha && $nuevaHora) {
            $this->fecha_inicio = Carbon::parse("$nuevaFecha $nuevaHora");
        }

        $this->estado = 'PENDIENTE';
        $this->save();
    }
    /**
     * Se llama cuando el pago de anticipo/completo queda CONFIRMADO.
     * Si la sesión no existe aún, la crea en estado CONFIRMADA.
     * Si ya existe (ej. el cliente re-subió un comprobante tras un rechazo previo
     * y este es un segundo pago que confirma algo), no la duplica.
     */
    public function confirmar(): Sesion
    {
        if (! $this->estaAprobada()) {
            throw new \LogicException(
                "No se puede confirmar la sesión de una reserva en estado {$this->estado}. Debe estar APROBADA."
            );
        }

        $sesionExistente = $this->sesion()->first(); // query fresca, no cachea null
        if ($sesionExistente) {
            return $sesionExistente;
        }

        $sesion = $this->sesion()->create([
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin'    => $this->fecha_fin,
            'lugar'        => $this->lugar,
            'estado'       => 'CONFIRMADA',
        ]);

        $this->setRelation('sesion', $sesion); // actualiza la caché con el valor real

        return $sesion;
    }

    /**
     * Se llama cuando el pago FINAL queda CONFIRMADO.
     * Habilita la entrega de la galería final dentro de la sesión existente.
     */
    public function habilitarEntregaFinal(): void
    {
        if (! $this->sesion) {
            throw new \LogicException('No existe una sesión asociada a esta reserva para habilitar la entrega final.');
        }

        $sesion = $this->sesion();

        $sesion->update(['fecha_finalizacion' => now()]); // o la fecha de junio que necesites
    }


}
