<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\CollectionTrackingController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\PayableImportController;
use App\Http\Controllers\Platform\CompanyApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceivableImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('collections.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/cuenta-pendiente', function () {
    return view('account.pending');
})->middleware('auth')->name('account.pending');

Route::middleware(['auth', 'platform.admin'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/empresas', [CompanyApprovalController::class, 'index'])->name('companies.index');
    Route::post('/empresas/{company}/aprobar', [CompanyApprovalController::class, 'approve'])->name('companies.approve');
    Route::post('/empresas/{company}/suspender', [CompanyApprovalController::class, 'suspend'])->name('companies.suspend');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/receivables/import', [ReceivableImportController::class, 'create'])->name('receivables.import');
    Route::post('/receivables/import', [ReceivableImportController::class, 'store'])->name('receivables.import.store');

    Route::get('/payables/import', [PayableImportController::class, 'create'])->name('payables.import');
    Route::post('/payables/import', [PayableImportController::class, 'store'])->name('payables.import.store');

    Route::get('/gestion-cobros', [CollectionTrackingController::class, 'index'])->name('collections.index');
    Route::get('/gestion-cobros/proyeccion', [CollectionTrackingController::class, 'forecast'])->name('collections.forecast');
    Route::get('/gestion-cobros/flujo-de-caja', [CollectionTrackingController::class, 'cashFlow'])->name('collections.cashflow');
    Route::get('/gestion-cobros/{collectionTracking}', [CollectionTrackingController::class, 'show'])->name('collections.show');
    Route::post('/gestion-cobros/{collectionTracking}/actividades', [CollectionTrackingController::class, 'addActivity'])->name('collections.activities.store');
    Route::post('/gestion-cobros/{collectionTracking}/recordatorio', [CollectionTrackingController::class, 'sendReminder'])->name('collections.remind');
    Route::post('/cuentas-por-cobrar/{receivable}/gestionar', [CollectionTrackingController::class, 'createFromReceivable'])->name('collections.from-receivable');

    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');
    Route::post('/api-tokens', [ApiTokenController::class, 'store'])->name('api-tokens.store');
    Route::delete('/api-tokens/{tokenId}', [ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

    Route::get('/mi-empresa/usuarios', [CompanyUserController::class, 'index'])->name('company.users.index');
    Route::post('/mi-empresa/usuarios', [CompanyUserController::class, 'store'])->name('company.users.store');
});

require __DIR__.'/auth.php';
