<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Repositories\ReservaRepository;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;
use App\Repositories\Contracts\ClienteRepositoryInterface;
use App\Repositories\ClienteRepository;
use App\Repositories\Contracts\FotografoRepositoryInterface;
use App\Repositories\FotografoRepository;
use App\Repositories\Contracts\CatalogoRepositoryInterface;
use App\Repositories\CatalogoRepository;
use App\Repositories\Contracts\SesionRepositoryInterface;
use App\Repositories\SesionRepository;
use App\Repositories\Contracts\FotografiaRepositoryInterface;
use App\Repositories\FotografiaRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ReservaRepositoryInterface::class,
            ReservaRepository::class,
        );

        $this->app->bind(
            UsuarioRepositoryInterface::class,
            UsuarioRepository::class,
        );

        $this->app->bind(
            ClienteRepositoryInterface::class,
            ClienteRepository::class,
        );

        $this->app->bind(
            FotografoRepositoryInterface::class,
            FotografoRepository::class,
        );

        $this->app->bind(
            CatalogoRepositoryInterface::class,
            CatalogoRepository::class,
        );

        $this->app->bind(
            SesionRepositoryInterface::class,
            SesionRepository::class,
        );

        $this->app->bind(
            FotografiaRepositoryInterface::class,
            FotografiaRepository::class,
        );
    }

    public function boot(): void {}
}
