<?php

use App\Http\Controllers\Api\PayablesApiController;
use App\Http\Controllers\Api\ReceivablesApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/receivables', [ReceivablesApiController::class, 'index']);
    Route::post('/receivables', [ReceivablesApiController::class, 'store']);

    Route::get('/payables', [PayablesApiController::class, 'index']);
    Route::post('/payables', [PayablesApiController::class, 'store']);
});
