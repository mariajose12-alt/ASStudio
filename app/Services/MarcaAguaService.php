<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;

class MarcaAguaService
{
    private const TEXTO = 'FOTOS SIN EDITAR, NO PUBLICAR';

    /**
     * Estampa el texto una sola vez, centrado y horizontal, sobre la
     * imagen. Devuelve la imagen re-codificada como JPEG.
     *
     * @param  string  $contenidoImagen  Bytes crudos de la imagen original
     */
    public function aplicar(string $contenidoImagen): string
    {
        $fuente = resource_path('fonts/marca-agua.ttf');

        if (! file_exists($fuente)) {
            throw new \RuntimeException(
                'Falta el archivo de fuente para la marca de agua: '
                . 'coloca un .ttf en resources/fonts/marca-agua.ttf'
            );
        }

        $imagen = Image::read($contenidoImagen);

        $ancho = $imagen->width();
        $alto  = $imagen->height();

        // Tamaño de letra proporcional al ancho, para que se lea bien
        // tanto en una foto chica como en una de varios miles de px.
        $tamanoFuente = max(20, (int) round($ancho / 22));

        $imagen->text(self::TEXTO, (int) ($ancho / 2), (int) ($alto / 2), function ($font) use ($fuente, $tamanoFuente) {
            $font->filename($fuente);
            $font->size($tamanoFuente);
            $font->color('rgba(255, 255, 255, 0.75)');
            $font->align('center');
            $font->valign('middle');
        });

        return (string) $imagen->toJpeg(quality: 90);
    }
}
