<?php

namespace App\Jobs;

use App\Models\Fotografia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class GenerarThumbnailFoto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly int $fotografiaId,
    ) {}

    public function handle(): void
    {
        $foto = Fotografia::find($this->fotografiaId);

        if (! $foto || $foto->url_thumb) {
            return; // ya no existe, o ya tiene thumb (evita duplicar en reintentos)
        }

        try {
            // Descargamos el archivo original de R2 a un temporal local para procesarlo
            $contenido = Storage::disk('r2')->get($foto->url);

            $thumb = Image::read($contenido)
                ->cover(600, 400)
                ->toJpeg(quality: 75);

            $nombreThumb = pathinfo($foto->url, PATHINFO_FILENAME) . '_thumb.jpg';
            $directorio  = pathinfo($foto->url, PATHINFO_DIRNAME);
            $pathThumb   = "{$directorio}/thumbs/{$nombreThumb}";

            Storage::disk('r2')->put($pathThumb, (string) $thumb);

            $foto->update(['url_thumb' => $pathThumb]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo generar thumbnail', [
                'fotografia_id' => $this->fotografiaId,
                'error'         => $e->getMessage(),
            ]);
        }
    }
}
