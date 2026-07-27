<?php

use App\Http\Controllers\CollectionTrackingController;
use App\Http\Controllers\PayableImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceivableImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('collections.index');
})->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::post('/cuentas-por-cobrar/{receivable}/gestionar', [CollectionTrackingController::class, 'createFromReceivable'])->name('collections.from-receivable');
});

require __DIR__.'/auth.php';
