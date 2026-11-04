<?php

use App\Http\Controllers\Api\AsesorController;
use App\Http\Controllers\Api\CatalogoController;
use App\Http\Controllers\Api\NovedadesController;
use App\Http\Controllers\Api\PreciosController;
use App\Http\Controllers\Api\PuenteController;
use App\Http\Middleware\VerificarFirmaPuente;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:120,1')->group(function () {
    Route::get('catalogo', CatalogoController::class)->name('api.catalogo');
    Route::get('precios', PreciosController::class)->name('api.precios');
    Route::get('novedades', [NovedadesController::class, 'indice'])->name('api.novedades');
});

Route::get('foto', [NovedadesController::class, 'foto'])
    ->middleware(['throttle:600,1', 'signed:relative'])
    ->name('api.foto');

Route::middleware('throttle:asesor')->group(function () {
    Route::post('asesor', [AsesorController::class, 'asesor'])->name('api.asesor');
    Route::post('recomendar', [AsesorController::class, 'recomendar'])->name('api.recomendar');
});

Route::prefix('puente')->middleware(['throttle:300,1', VerificarFirmaPuente::class])->group(function () {
    Route::get('huellas', [PuenteController::class, 'huellas']);
    Route::post('cambios', [PuenteController::class, 'cambios']);
    Route::post('latido', [PuenteController::class, 'latido']);
});
