<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\SalesController;

Route::middleware('auth.user')->group(function () {
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/create', [SalesController::class, 'create'])->name('create');

        Route::get('/{sale}', [SalesController::class, 'show'])->name('show');
        Route::post('/store', [SalesController::class, 'store'])->name('store');

        Route::get('/{sale}/edit', [SalesController::class, 'edit'])->name('edit');
        Route::put('/{sale}', [SalesController::class, 'update'])->name('update');
        Route::delete('/{sale}', [SalesController::class, 'destroy'])->name('destroy');

        Route::get('/{sale}/receipt', [SalesController::class, 'printReceipt'])
            ->name('printReceipt');

        Route::get('/api/search-products', [SalesController::class, 'searchProducts'])
            ->name('search-products');

        Route::get('/api/statistics', [SalesController::class, 'statistics'])
            ->name('statistics');
    });

});
