<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SolicitudEstudioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // público, sin auth — igual que el resto del wizard
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'telefono' => ['required', 'string', 'max:20'],
            'invitados' => ['nullable', 'string', 'max:1000'],
            'finalidad' => ['required', 'in:fotografia,video,podcast,contenido_personal,evento'],
            'cantidad_personas' => ['required', 'integer', 'min:1', 'max:50'],
            'color_fondo_adicional' => ['boolean'],
            'color_fondo' => ['required_if:color_fondo_adicional,true', 'nullable', 'string', 'max:50'],
            'iluminacion' => ['required', 'in:luz_fija,flashes,luz_natural,luz_fija_flash,otro'],
            'iluminacion_otro' => ['required_if:iluminacion,otro', 'nullable', 'string', 'max:100'],
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ];
    }
}
