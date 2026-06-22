<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\CatalogoRepositoryInterface;
use App\Repositories\Contracts\ClienteRepositoryInterface;
use App\Repositories\Contracts\FotografiaRepositoryInterface;
use App\Repositories\Contracts\FotografoRepositoryInterface;
use App\Repositories\Contracts\NotificacionRepositoryInterface;
use App\Repositories\Contracts\PaqueteRepositoryInterface;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Repositories\Contracts\SesionRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;

use App\Repositories\CatalogoRepository;
use App\Repositories\ClienteRepository;
use App\Repositories\FotografiaRepository;
use App\Repositories\FotografoRepository;
use App\Repositories\NotificacionRepository;
use App\Repositories\PaqueteRepository;
use App\Repositories\ReservaRepository;
use App\Repositories\SesionRepository;
use App\Repositories\UsuarioRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CatalogoRepositoryInterface::class,     CatalogoRepository::class);
        $this->app->bind(ClienteRepositoryInterface::class,      ClienteRepository::class);
        $this->app->bind(FotografiaRepositoryInterface::class,   FotografiaRepository::class);
        $this->app->bind(FotografoRepositoryInterface::class,    FotografoRepository::class);
        $this->app->bind(NotificacionRepositoryInterface::class, NotificacionRepository::class);
        $this->app->bind(PaqueteRepositoryInterface::class,      PaqueteRepository::class);
        $this->app->bind(ReservaRepositoryInterface::class,      ReservaRepository::class);
        $this->app->bind(SesionRepositoryInterface::class,       SesionRepository::class);
        $this->app->bind(UsuarioRepositoryInterface::class,      UsuarioRepository::class);
    }

    public function boot(): void {}
}
