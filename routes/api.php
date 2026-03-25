<?php

use App\Http\Controllers\Api\System\AcaoController;
use Illuminate\Support\Facades\Route;

Route::get('/acoes', [AcaoController::class, 'index']);

Route::middleware('manager.token')->group(function () {
});