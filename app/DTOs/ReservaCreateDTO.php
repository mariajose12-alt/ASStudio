<?php

namespace App\DTOs;

class ReservaCreateDTO
{
    public function __construct(
        public readonly int     $cliente_id,
        public readonly int     $paquete_id,
        public readonly int     $catalogo_id,
        public readonly int     $fotografo_id,
        public readonly string  $tipo,
        public readonly string  $descripcion, // La descripcion nunca puede ser null
        public readonly string  $fecha_inicio,
        public readonly ?string $fecha_fin = null,
        public readonly float   $precio_total,
        public readonly ?string $lugar = null,
    ) {}

    // Se arma desde los datos de sesión en el Controller
    public static function fromSesion(
        array $paso1,
        array $paso2,
        int   $cliente_id,
        int   $fotografo_id,
        float $precio_total
    ): self {
        return new self(
            cliente_id:   $cliente_id,
            paquete_id:   $paso1['paquete_id'],
            catalogo_id:  $paso1['catalogo_id'],
            fotografo_id: $fotografo_id,
            tipo:         $paso1['tipo'],
            descripcion:  $paso2['descripcion'],
            fecha_inicio: $paso2['fecha'] . ' ' . $paso2['hora'],
            fecha_fin:    null,
            precio_total: $precio_total,
            lugar:        $paso1['lugar'] ?? null,
        );
    }
}
