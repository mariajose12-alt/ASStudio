<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Repositories\ReservaRepository;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registro del repositorio de Reservas
        $this->app->bind(
            ReservaRepositoryInterface::class,
            ReservaRepository::class,
        );

        // Registro del repositorio de Usuarios (Nuevo)
        $this->app->bind(
            UsuarioRepositoryInterface::class,
            UsuarioRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
