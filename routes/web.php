<?php

use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\PaqueteController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\FotografoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ClienteReservaController;

// PÚBLICAS

Route::get('/', function () {

    $esAdmin = auth()->check() && auth()->user()->esAdministrador();

    return view('landing', compact('esAdmin'));
});

Route::get('/api/paquetes/{catalogo}', function ($catalogoId) {
    $catalogo = \App\Models\Catalogo::with('paquetes')->find($catalogoId);

    if (!$catalogo) return response()->json([]);

    return $catalogo->paquetes
        ->where('activo', true)
        ->values()
        ->map(fn($p) => [
            'id'          => $p->id,
            'nombre'      => $p->nombre,
            'precio_base' => $p->precio_base,
        ]);
});

// AUTENTICACIÓN

Route::get('/login',    [LoginController::class, 'showForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register',[RegisterController::class, 'register'])->name('register.post');

// AUTENTICADO — sin rol específico (perfil + reservas cliente)
Route::middleware('auth')->group(function () {

    // Perfil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// DASHBOARD ADMINISTRADOR
Route::middleware(['auth', 'rol:ADMINISTRADOR'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::resource('empleados', EmpleadoController::class);
        Route::resource('paquetes',  PaqueteController::class);
        Route::resource('catalogos', CatalogoController::class);

        Route::get('reservas',                    [AdminController::class, 'reservasIndex'])->name('reservas.index');
        Route::get('reservas/{reserva}',          [AdminController::class, 'reservasShow'])->name('reservas.show');
        Route::patch('reservas/{reserva}/estado', [AdminController::class, 'reservasCambiarEstado'])->name('reservas.estado');
    });

// DASHBOARD FOTÓGRAFO
Route::middleware(['auth', 'rol:FOTOGRAFO'])
    ->prefix('fotografo')
    ->name('fotografo.')
    ->group(function () {
        Route::get('/dashboard', [FotografoController::class, 'dashboard'])->name('dashboard');
        // Route::resource('sesiones', SesionController::class);
        // Route::get('agenda', [AgendaController::class, 'index'])->name('agenda');
    });

// DASHBOARD CLIENTE
Route::middleware(['auth', 'rol:CLIENTE']) ->prefix('cliente') ->name('cliente.') ->group(function () {
        Route::get('/dashboard', [ClienteController::class, 'dashboard'])->name('dashboard');
        Route::get('/perfil',  [ClienteController::class, 'perfil'])->name('perfil');
        Route::put('/perfil',  [ClienteController::class, 'actualizarPerfil'])->name('perfil.update');


        // Ver reservas (nuevo controller)
        Route::get('reservas',          [ClienteReservaController::class, 'index'])->name('reservas.index');
        //Route::get('reservas/{reserva}',[ClienteReservaController::class, 'show'])->name('reservas.show');


        // Flujo de reservas (movido bajo prefijo cliente)
        Route::get('reservas',             [ReservaController::class, 'index'])->name('reservas.index');

        Route::get('reservas/paso1',       [ReservaController::class, 'paso1'])->name('reservas.paso1');
        Route::post('reservas/paso1',      [ReservaController::class, 'guardarPaso1'])->name('reservas.guardarPaso1');

        Route::get('reservas/paso2',       [ReservaController::class, 'paso2'])->name('reservas.paso2');
        Route::post('reservas/paso2',      [ReservaController::class, 'guardarPaso2'])->name('reservas.guardarPaso2');

        Route::get('reservas/paso3',       [ReservaController::class, 'paso3'])->name('reservas.paso3');
        Route::post('reservas/paso3',      [ReservaController::class, 'guardarPaso3'])->name('reservas.guardarPaso3');

        Route::get('reservas/paso4',       [ReservaController::class, 'paso4'])->name('reservas.paso4');
        Route::post('reservas/enviar',     [ReservaController::class, 'enviar'])->name('reservas.enviar');


    });

Route::resource('admin/paquetes', PaqueteController::class)
    ->names('admin.paquetes')
    ->parameters(['paquetes' => 'paquete']);

Route::resource('admin/catalogos', CatalogoController::class)
    ->names('admin.catalogos')
    ->parameters(['catalogos' => 'catalogo']);

require __DIR__.'/auth.php';
