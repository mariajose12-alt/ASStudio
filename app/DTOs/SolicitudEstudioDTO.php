<?php

namespace App\DTOs;

class SolicitudEstudioDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly string $telefono,
        public readonly ?string $invitados,
        public readonly string $finalidad,
        public readonly int $cantidad_personas,
        public readonly bool $color_fondo_adicional,
        public readonly ?string $color_fondo,
        public readonly string $iluminacion,
        public readonly ?string $iluminacion_otro,
        public readonly string $fecha,
        public readonly string $hora_inicio,
        public readonly string $hora_fin,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'],
            apellido: $data['apellido'],
            email: $data['email'],
            telefono: $data['telefono'],
            invitados: $data['invitados'] ?? null,
            finalidad: $data['finalidad'],
            cantidad_personas: (int) $data['cantidad_personas'],
            color_fondo_adicional: (bool) ($data['color_fondo_adicional'] ?? false),
            color_fondo: $data['color_fondo'] ?? null,
            iluminacion: $data['iluminacion'],
            iluminacion_otro: $data['iluminacion_otro'] ?? null,
            fecha: $data['fecha'],
            hora_inicio: $data['hora_inicio'],
            hora_fin: $data['hora_fin'],
        );
    }
}
