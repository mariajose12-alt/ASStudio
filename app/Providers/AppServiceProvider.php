<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Repositories\ReservaRepository;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use App\Repositories\UsuarioRepository;
use App\Repositories\Contracts\ClienteRepositoryInterface;
use App\Repositories\ClienteRepository;

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

        // Registro del repositorio de Usuarios
        $this->app->bind(
            UsuarioRepositoryInterface::class,
            UsuarioRepository::class,
        );

        // Registro del repositorio de Clientes (Nuevo)
        $this->app->bind(
            ClienteRepositoryInterface::class,
            ClienteRepository::class,
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
