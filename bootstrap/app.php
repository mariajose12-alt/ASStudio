<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
            'verificado' => \App\Http\Middleware\CheckVerificado::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->routeIs('login*') && ! $request->expectsJson()) {
                $segundos = $e->getHeaders()['Retry-After'] ?? 60;
                $minutos  = (int) ceil($segundos / 60);

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => $minutos > 1
                            ? "Demasiados intentos. Intenta de nuevo en {$minutos} minutos."
                            : 'Demasiados intentos. Intenta de nuevo en 1 minuto.',
                    ]);
            }
        });
    })->create();
