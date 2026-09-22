<?php

use App\Http\Controllers\Api\ArchivoAdjuntoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\SolicitudController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('catalogos')->group(function () {
        Route::get('/roles', [CatalogoController::class, 'roles']);
        Route::get('/estados', [CatalogoController::class, 'estados']);
        Route::get('/tipos', [CatalogoController::class, 'tipos']);
        Route::get('/prioridades', [CatalogoController::class, 'prioridades']);
        Route::get('/ubicaciones', [CatalogoController::class, 'ubicaciones']);
        Route::get('/recursos', [CatalogoController::class, 'recursos']);
    });

    Route::middleware('role:Administrativo')->group(function () {
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/reportes/gestion', [ReporteController::class, 'gestion']);
    });

    Route::apiResource('solicitudes', SolicitudController::class)
        ->parameters(['solicitudes' => 'solicitud']);

    Route::prefix('solicitudes/{solicitud}')->group(function () {
        Route::patch('/clasificar', [SolicitudController::class, 'clasificar']);
        Route::patch('/estado', [SolicitudController::class, 'cambiarEstado']);
        Route::post('/asignar', [SolicitudController::class, 'asignar']);
        Route::get('/asignaciones', [SolicitudController::class, 'asignaciones']);

        Route::get('/comentarios', [ComentarioController::class, 'index']);
        Route::post('/comentarios', [ComentarioController::class, 'store']);

        Route::get('/adjuntos', [ArchivoAdjuntoController::class, 'index']);
        Route::post('/adjuntos', [ArchivoAdjuntoController::class, 'store']);
        Route::delete('/adjuntos/{archivo}', [ArchivoAdjuntoController::class, 'destroy']);

        Route::get('/historial', [HistorialController::class, 'index']);
    });
});
