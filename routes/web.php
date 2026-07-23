<?php

use App\Http\Controllers\ActivacionCuentaController;
use App\Http\Controllers\BloqueoEstudioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClientePagoController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\FotografiaController;
use App\Http\Controllers\FotografoNominaController;
use App\Http\Controllers\FotografoReservaController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PasswordOlvidadoController;
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
use App\Http\Controllers\SolicitudAyudanteController;

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

Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
    Route::get('/', [NotificacionController::class, 'index'])->name('index');
    Route::patch('/marcar-leidas', [NotificacionController::class, 'marcarLeidas'])->name('marcar-leidas');
    Route::patch('/{id}/marcar-leida', [NotificacionController::class, 'marcarLeida'])->name('marcar-leida');
});

// AUTENTICACIÓN

Route::get('/login',    [LoginController::class, 'showForm'])->name('login');
Route::post('/login',   [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
Route::post('/logout',  [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::get('/verificar-email/{token}', [RegisterController::class, 'verificar'])->name('verificar.email');
Route::post('/register',[RegisterController::class, 'register'])->middleware('throttle:5,1')->name('register.post');
Route::get('/auth/google',          [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
Route::get('/activar-cuenta/{usuario}/{token}', [ActivacionCuentaController::class, 'mostrar'])->name('activacion.mostrar');
Route::post('/activar-cuenta/{usuario}/{token}', [ActivacionCuentaController::class, 'procesar'])->name('activacion.procesar');
Route::get('/olvide-password',  [PasswordOlvidadoController::class, 'mostrarFormulario'])->name('password.olvidada.form');
Route::post('/olvide-password', [PasswordOlvidadoController::class, 'enviarLink'])->middleware('throttle:3,1')->name('password.olvidada.enviar');

// AUTENTICADO — sin rol específico (perfil + reservas cliente)
Route::middleware('auth')->group(function () {

    // Perfil
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
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
        Route::get('/nomina/{nomina}/pdf', [AdminController::class, 'nominaExportarPdf'])->name('nomina.pdf');
        Route::post('/nomina/{nomina}/confirmar', [AdminController::class, 'nominaConfirmar'])->name('nomina.confirmar');
        Route::post('/nomina/{nomina}/pagar', [AdminController::class, 'nominaPagar'])->name('nomina.pagar');

        Route::get('/nomina/disputa/{detalle}', [AdminController::class, 'nominaDisputaShow'])->name('nomina.disputa.show');
        Route::post('/nomina/disputa/{detalle}/participacion', [AdminController::class, 'nominaDisputaAgregarParticipacion'])->name('nomina.disputa.agregar');
        Route::delete('/nomina/disputa/{detalle}/participacion/{participacion}', [AdminController::class, 'nominaDisputaEliminarParticipacion'])->name('nomina.disputa.eliminar');
        Route::post('/nomina/disputa/{detalle}/aceptar', [AdminController::class, 'nominaDisputaAceptar'])->name('nomina.disputa.aceptar');
        Route::post('/nomina/disputa/{detalle}/rechazar', [AdminController::class, 'nominaDisputaRechazar'])->name('nomina.disputa.rechazar');
        Route::get('/metas', [AdminController::class, 'metasEdit'])->name('metas.edit');
        Route::post('/metas', [AdminController::class, 'metasActualizar'])->name('metas.actualizar');

        Route::post('/nomina/configuracion/parametros', [AdminController::class, 'nominaConfiguracionParametros'])->name('nomina.configuracion.parametros');
        Route::post('/nomina/configuracion/incentivos', [AdminController::class, 'nominaConfiguracionIncentivos'])->name('nomina.configuracion.incentivos');


        Route::resource('empleados', EmpleadoController::class);
        Route::resource('paquetes',  PaqueteController::class);
        Route::resource('catalogos', CatalogoController::class);

        Route::get('reservas',                    [AdminController::class, 'reservasIndex'])->name('reservas.index');
        Route::get('reservas/{reserva}',          [AdminController::class, 'reservasShow'])->name('reservas.show');

        Route::get('pagos',                 [AdminController::class, 'pagosIndex'])->name('pagos.index');
        Route::get('pagos/{pago}',          [AdminController::class, 'pagosRevisar'])->name('pagos.revisar');
        Route::post('pagos/{pago}/aprobar', [AdminController::class, 'pagosAprobar'])->name('pagos.aprobar');
        Route::post('pagos/{pago}/rechazar',[AdminController::class, 'pagosRechazar'])->name('pagos.rechazar');

        Route::get('estudio',                              [BloqueoEstudioController::class, 'index'])   ->name('estudio');
        Route::get('estudio/eventos',                      [BloqueoEstudioController::class, 'eventos'])  ->name('estudio.eventos');
        Route::post('estudio',                             [BloqueoEstudioController::class, 'store'])    ->name('estudio.store');
        Route::delete('estudio/{bloqueo}',                 [BloqueoEstudioController::class, 'destroy'])  ->name('estudio.destroy');
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
        Route::post('fotografias/{id}/aprobar',         [FotografiaController::class, 'aprobar'])         ->name('fotografias.aprobar');
        Route::delete('fotografias/{id}/rechazar',      [FotografiaController::class, 'rechazar'])        ->name('fotografias.rechazar');

        // Solicitudes de ayudante
        Route::post('sesiones/{sesion}/solicitudes-ayudante', [SolicitudAyudanteController::class, 'store'])->name('solicitudes-ayudante.store');
        Route::post('solicitudes-ayudante/{solicitud}/postular', [SolicitudAyudanteController::class, 'postularse'])->name('solicitudes-ayudante.postularse');
        Route::post('solicitudes-ayudante/{solicitud}/postulaciones/{postulacion}/confirmar', [SolicitudAyudanteController::class, 'confirmar'])->name('solicitudes-ayudante.confirmar');
        Route::post('solicitudes-ayudante/{solicitud}/postulaciones/{postulacion}/rechazar', [SolicitudAyudanteController::class, 'rechazar'])->name('solicitudes-ayudante.rechazar');
        Route::post('solicitudes-ayudante/{solicitud}/cancelar', [SolicitudAyudanteController::class, 'cancelar'])->name('solicitudes-ayudante.cancelar');

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

        // Estas dos no requieren verificación
        Route::get('/verificacion-pendiente', fn() => view('cliente.verificacion-pendiente'))->name('verificacion.pendiente');
        Route::post('/verificacion-reenviar', [RegisterController::class, 'reenviarVerificacion'])->name('verificacion.reenviar');

        Route::middleware('verificado')->group(function () {

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

    });

Route::resource('admin/paquetes', PaqueteController::class)
    ->names('admin.paquetes')
    ->parameters(['paquetes' => 'paquete']);

Route::resource('admin/catalogos', CatalogoController::class)
    ->names('admin.catalogos')
    ->parameters(['catalogos' => 'catalogo']);

Route::get('/terminos-condiciones', fn() => view('reservas.terminos-condiciones'))->name('terminos');

require __DIR__.'/auth.php';
