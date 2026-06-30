<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClientePagoController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\FotografiaController;
use App\Http\Controllers\FotografoNominaController;
use App\Http\Controllers\FotografoReservaController;
use App\Http\Controllers\SesionController;
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
use App\Http\Controllers\GaleriaController;
use App\Http\Controllers\Auth\GoogleController;

// PÚBLICAS

Route::get('/', function () {

    $esAdmin = auth()->check() && auth()->user()->esAdministrador();

    return view('landing', compact('esAdmin'));
});

Route::get('/admin/comprobante/{comprobante}/imagen', function (App\Models\Comprobante $comprobante) {
    abort_unless(auth()->user()->esAdministrador(), 403);
    $contenido = Storage::disk('r2')->get($comprobante->archivo_key);
    $mime = Storage::disk('r2')->mimeType($comprobante->archivo_key);
    return response($contenido)->header('Content-Type', $mime);
})->middleware(['auth'])->name('admin.comprobante.imagen');

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

Route::get('/disponibilidad/fechas',
    [DisponibilidadController::class, 'fechasOcupadas']
)->name('disponibilidad.fechas');

Route::get('/catalogo', function () {
    return view('catalogo');
})->name('catalogo');

Route::get('/estudio', function () {
    return view('estudio');
})->name('estudio');

//pruebas para los emails
Route::get('/preview-mail', function () {
    $reserva = App\Models\Reserva::first();

    return new App\Mail\ReservaModificadaCliente($reserva);
});

// AUTENTICACIÓN

Route::get('/login',    [LoginController::class, 'showForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login'])->name('login.post');
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register',[RegisterController::class, 'register'])->name('register.post');
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

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
        Route::get('/estudio',   [AdminController::class, 'estudio'])->name('estudio');

        Route::get('/nomina',    [AdminController::class, 'nomina'])->name('nomina');
        Route::post('/nomina/calcular', [AdminController::class, 'calcularNomina'])->name('nomina.calcular');
        Route::get('/nomina/{nomina}/resumen', [AdminController::class, 'nominaResumen'])->name('nomina.resumen');
        Route::post('/nomina/{nomina}/confirmar', [AdminController::class, 'nominaConfirmar'])->name('nomina.confirmar');
        Route::get('/nomina/disputa/{detalle}', [AdminController::class, 'nominaDisputaShow'])->name('nomina.disputa.show');
        Route::post('/nomina/disputa/{detalle}/participacion', [AdminController::class, 'nominaDisputaAgregarParticipacion'])->name('nomina.disputa.agregar');
        Route::delete('/nomina/disputa/{detalle}/participacion/{participacion}', [AdminController::class, 'nominaDisputaEliminarParticipacion'])->name('nomina.disputa.eliminar');
        Route::post('/nomina/disputa/{detalle}/aceptar', [AdminController::class, 'nominaDisputaAceptar'])->name('nomina.disputa.aceptar');
        Route::post('/nomina/disputa/{detalle}/rechazar', [AdminController::class, 'nominaDisputaRechazar'])->name('nomina.disputa.rechazar');

        Route::resource('empleados', EmpleadoController::class);
        Route::resource('paquetes',  PaqueteController::class);
        Route::resource('catalogos', CatalogoController::class);

        Route::get('reservas',                    [AdminController::class, 'reservasIndex'])->name('reservas.index');
        Route::get('reservas/{reserva}',          [AdminController::class, 'reservasShow'])->name('reservas.show');
        Route::patch('reservas/{reserva}/estado', [AdminController::class, 'reservasCambiarEstado'])->name('reservas.estado');

        Route::get('pagos',                 [AdminController::class, 'pagosIndex'])->name('pagos.index');
        Route::get('pagos/{pago}',          [AdminController::class, 'pagosRevisar'])->name('pagos.revisar');
        Route::post('pagos/{pago}/aprobar', [AdminController::class, 'pagosAprobar'])->name('pagos.aprobar');
        Route::post('pagos/{pago}/rechazar',[AdminController::class, 'pagosRechazar'])->name('pagos.rechazar');

    });

// DASHBOARD FOTÓGRAFO
Route::middleware(['auth', 'rol:FOTOGRAFO'])
    ->prefix('fotografo')
    ->name('fotografo.')
    ->group(function () {
        Route::get('/dashboard',                 [FotografoController::class, 'dashboard'])->name('dashboard');
        Route::get('/calendario',                [FotografoController::class, 'calendario'])->name('calendario');
        Route::get('/calendario/reservas',       [FotografoController::class, 'reservasJson'])->name('calendario.json');
        Route::get('/upload',                    [FotografoController::class, 'upload'])->name('upload');

        Route::get('reservas',                   [FotografoReservaController::class, 'index'])->name('reservas.index');
        Route::get('reservas/{reserva}',         [FotografoReservaController::class, 'show'])->name('reservas.show');
        Route::post('reservas/{reserva}/accion', [FotografoReservaController::class, 'procesarAccion'])->name('reservas.accion');

        // Sesiones
        Route::get('sesiones',               [SesionController::class, 'index'])->name('sesiones.index');
        Route::post('sesiones/{id}/iniciar', [SesionController::class, 'iniciar'])->name('sesiones.iniciar');

        // Fotografías
        Route::get('sesiones/{id}/fotografias/create',  [FotografiaController::class, 'create'])         ->name('fotografias.create');
        Route::post('sesiones/{id}/fotografias',        [FotografiaController::class, 'store'])           ->name('fotografias.store');
        Route::patch('sesiones/{id}/entregar',          [FotografiaController::class, 'marcarEntregada']) ->name('fotografias.entregar');

        //Confirmacionde detalle Nomina
        Route::get('nomina',                      [FotografoNominaController::class, 'index'])->name('nomina.index');
        Route::get('nomina/{detalle}',             [FotografoNominaController::class, 'show'])->name('nomina.show');
        Route::post('nomina/{detalle}/confirmar', [FotografoNominaController::class, 'confirmar'])->name('nomina.confirmar');
        Route::post('nomina/{detalle}/ajuste',    [FotografoNominaController::class, 'reportarAjuste'])->name('nomina.ajuste');
    });

// DASHBOARD CLIENTE
Route::middleware(['auth', 'rol:CLIENTE'])
    ->prefix('cliente')
    ->name('cliente.')
    ->group(function () {

        Route::get('/dashboard', [ClienteController::class, 'dashboard'])->name('dashboard');
        Route::get('/perfil',    [ClienteController::class, 'perfil'])->name('perfil');
        Route::put('/perfil',    [ClienteController::class, 'actualizarPerfil'])->name('perfil.update');
        Route::get('/galeria',   [ClienteController::class, 'galeria'])->name('galeria');

        // Reservas
        Route::get('reservas/paso1',   [ReservaController::class, 'paso1'])->name('reservas.paso1');
        Route::post('reservas/paso1',  [ReservaController::class, 'guardarPaso1'])->name('reservas.guardarPaso1');
        Route::get('reservas/paso2',   [ReservaController::class, 'paso2'])->name('reservas.paso2');
        Route::post('reservas/paso2',  [ReservaController::class, 'guardarPaso2'])->name('reservas.guardarPaso2');
        Route::get('reservas/paso3',   [ReservaController::class, 'paso3'])->name('reservas.paso3');
        Route::post('reservas/paso3',  [ReservaController::class, 'guardarPaso3'])->name('reservas.guardarPaso3');
        Route::get('reservas/paso4',   [ReservaController::class, 'paso4'])->name('reservas.paso4');
        Route::post('reservas/enviar', [ReservaController::class, 'enviar'])->name('reservas.enviar');

        Route::get('galeria',                          [GaleriaController::class, 'index'])             ->name('galeria');
        Route::get('galeria/{id}',                     [GaleriaController::class, 'show'])              ->name('galeria.show');
        Route::post('galeria/{id}/confirmar',          [GaleriaController::class, 'confirmar'])         ->name('galeria.confirmar');
        Route::get('galeria/{id}/final',               [GaleriaController::class, 'final'])             ->name('galeria.final');
        Route::post('galeria/{id}/recepcion',          [GaleriaController::class, 'confirmarRecepcion'])->name('galeria.recepcion');
        Route::get('galeria/foto/{id}/descargar',      [GaleriaController::class, 'descargar'])         ->name('galeria.descargar');
        Route::get('galeria/{id}/zip/{tipo}',          [GaleriaController::class, 'descargarZip'])      ->name('galeria.zip');
        Route::get('reservas/{reserva}',               [ClienteReservaController::class, 'show'])->name('reservas.show');

        Route::patch('reservas/{reserva}/responder-sugerencia', [ClienteReservaController::class, 'responderSugerencia'])->name('reservas.responder-sugerencia');

        // Índice al final
        Route::get('reservas',         [ReservaController::class, 'index'])->name('reservas.index');

        Route::get('pagos',                     [ClientePagoController::class, 'index'])->name('pagos.index');
        Route::get('pagos/{pago}/comprobante',  [ClientePagoController::class, 'formulario'])->name('pagos.comprobante.form');
        Route::post('pagos/{pago}/comprobante', [ClientePagoController::class, 'guardar'])->name('pagos.comprobante.guardar');
    });

Route::resource('admin/paquetes', PaqueteController::class)
    ->names('admin.paquetes')
    ->parameters(['paquetes' => 'paquete']);

Route::resource('admin/catalogos', CatalogoController::class)
    ->names('admin.catalogos')
    ->parameters(['catalogos' => 'catalogo']);

Route::get('/terminos-condiciones', fn() => view('reservas.terminos-condiciones'))->name('terminos');

require __DIR__.'/auth.php';
