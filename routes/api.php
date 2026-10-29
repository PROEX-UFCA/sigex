<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Dados\DadosController;
use App\Http\Controllers\Api\System\AcaoController;
use Illuminate\Support\Facades\Route;

Route::get('/acoes', [AcaoController::class, 'index']);
Route::get('/acoes/{id}', [AcaoController::class, 'getById']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/sincronizar/acoes', [DadosController::class, 'sincronizarAcoes']);
    Route::post('/sincronizar/locais', [DadosController::class, 'sincronizarLocais']);
    Route::post('/sincronizar/membros', [DadosController::class, 'sincronizarMembros']);
});