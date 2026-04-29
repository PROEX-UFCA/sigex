<?php

use App\Http\Controllers\Api\System\AcaoController;
use Illuminate\Support\Facades\Route;

Route::get('/acoes', [AcaoController::class, 'index']);
Route::get('/acoes/{id}', [AcaoController::class, 'getById']);

Route::middleware('manager.token')->group(function () {
});