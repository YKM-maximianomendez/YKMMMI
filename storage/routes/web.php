<?php

use App\Http\Controllers\Files\DownloadEvidenciaController;
use App\Http\Controllers\Files\DownloadFORMAController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\ConsultarReparacionesPendientesController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\RegistrarActividadesReparacionController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\RegistrarEvidenciasController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\RegistrarMaterialUtilizadoController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\ResumenReparacionController;
use App\Http\Controllers\Mantenimiento\KioscoReparaciones\TerminarActividadReparacionController;
use App\Http\Controllers\Mantenimiento\OrdenReparacion\CerrarOrdenReparacionController;
use App\Http\Controllers\Mantenimiento\OrdenReparacion\ConfirmarCierreOrdenReparacionController;
use App\Http\Controllers\Mantenimiento\OrdenReparacionFalla\FallasProgramadasController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false, 'reset' => false]);

// 1. Catalogos
Route::prefix('catalogos')->as('catalogos.')
    ->group(function () {
        Route::resource('causa-falla', \App\Http\Controllers\Catalogos\CausaFallaController::class)->except(['create', 'show', 'edit']);
        Route::resource('clasificacion-falla', \App\Http\Controllers\Catalogos\ClasificacionFallaController::class)->except(['create', 'show', 'edit']);
        Route::resource('falla', \App\Http\Controllers\Catalogos\FallaController::class)->except(['create', 'show', 'edit']);
        Route::resource('actividad-reparacion', \App\Http\Controllers\Catalogos\ActividadReparacionController::class)->except(['create', 'show', 'edit']);
        Route::resource('numeroparte-modelo', \App\Http\Controllers\Catalogos\NumeroParteModeloController::class)->except(['create', 'show', 'edit']);
        Route::resource('numeroparte', \App\Http\Controllers\Catalogos\NumeroParteController::class)->except(['create', 'show', 'edit']);
        Route::resource('estacion', \App\Http\Controllers\Catalogos\EstacionController::class)->except(['create', 'edit']);
    });

// 2. Mantenimiento
Route::prefix('mantenimiento')->as('mantenimiento.')
    ->group(function () {
        Route::resource('ordenesreparacion', \App\Http\Controllers\Mantenimiento\OrdenReparacionController::class)->except(['destroy']);
        Route::resource('ordenesreparacion-falla', \App\Http\Controllers\Mantenimiento\OrdenReparacionFallaController::class)->except(['edit', 'update', 'destroy']);
        Route::resource('kiosco-reparaciones', \App\Http\Controllers\Mantenimiento\KioscoReparacionesController::class)->only('index');

        Route::put('/ordenreparacion-falla/programar/{id_orden}/{id_orden_falla}', \App\Http\Controllers\Mantenimiento\OrdenReparacionFalla\ProgramarFallaController::class)->name('ordenreparacion-falla.programar');
        Route::put('/ordenreparacion-falla/cerrar/{id_orden}/{id_orden_falla}', \App\Http\Controllers\Mantenimiento\OrdenReparacionFalla\CerrarFallaController::class)->name('ordenreparacion-falla.cerrar');

        Route::middleware('role:Lider ToolRoom')->put('/ordenreparacion/{id_orden}/cerrar', CerrarOrdenReparacionController::class)->name('ordenreparacion.cerrar');
        Route::middleware('role:Lider Prensas')->put('/ordenreparacion/{id_orden}/confirmar-cierre', ConfirmarCierreOrdenReparacionController::class)->name('ordenreparacion.confirmar-cierre');

        Route::resource('kiosco-reparaciones', \App\Http\Controllers\Mantenimiento\KioscoReparacionesController::class)->only('index');
        Route::post('/kiosco-reparaciones/store-actividades', RegistrarActividadesReparacionController::class)->name('kiosco-reparaciones.store-actividades');
        Route::post('/kiosco-reparaciones/store-materialutilizado', RegistrarMaterialUtilizadoController::class)->name('kiosco-reparaciones.store-materialutilizado');
        Route::post('/kiosco-reparaciones/store-evidencias', RegistrarEvidenciasController::class)->name('kiosco-reparaciones.store-evidencias');

        Route::get('/kiosco-reparaciones/reparaciones-pendientes/{id_orden_falla}', ConsultarReparacionesPendientesController::class)->name('kiosco-reparaciones.reparaciones-pendientes');
        Route::post('/kiosco-reparaciones/reparaciones-pendientes/{id_orden_falla}/terminar/{id_actividad_reparacion}', TerminarActividadReparacionController::class)->name('kiosco-reparaciones.reparaciones-pendientes.terminar');
        Route::get('/kiosco-reparaciones/resumen-reparacion/{id_orden_falla}', ResumenReparacionController::class)->name('kiosco-reparaciones.resumen-reparacion');

        Route::get('/ordenesreparacion-fallas/programadas', FallasProgramadasController::class)->name('ordenesprogramacion-fallas.programadas');
        
        Route::prefix('tablero')->as('tablero.')
            ->group(function () {
                Route::get('/control-correctivos', \App\Http\Controllers\Mantenimiento\Tablero\ControlCorrectivosController::class)->name('control-correctivos.index');
            });

        Route::prefix('reportes')->as('reportes.')
            ->group(function () {
                Route::prefix('ordenesreparacion')->as('ordenesreparacion.')
                    ->group(function () {
                        Route::get('/', [\App\Http\Controllers\Mantenimiento\Reportes\OrdenesReparacionController::class, 'index'])->name('index');
                    });
                Route::prefix('ordenesreparacion-fallas')->as('ordenesreparacion-fallas.')
                    ->group(function () {
                        Route::get('/', [\App\Http\Controllers\Mantenimiento\Reportes\OrdenesReparacionFallasController::class, 'index'])->name('index');
                    });
            });
    });

// 3. Seguridad
Route::prefix('seguridad')->as('seguridad.')
    ->group(function () {
        Route::resource('usuarios', \App\Http\Controllers\Seguridad\UsuarioController::class)->except('create', 'edit', 'show');
    });

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('download-FORMA/{id_orden}', DownloadFORMAController::class)->name('download-FORMA');
Route::get('download-evidencia/{id_orden_evidencia}', DownloadEvidenciaController::class)->name('download-evidencia');
