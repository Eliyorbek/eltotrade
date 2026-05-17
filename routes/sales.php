<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\SalesController;

Route::middleware('auth.user')->group(function () {
    Route::prefix('sales')->name('sales.')->group(function () {
        // CRUD Routes
        Route::get('/', [SalesController::class, 'index'])
            ->name('index');

        Route::get('/create', [SalesController::class, 'create'])
            ->name('create');

        Route::post('/store', [SalesController::class, 'store'])
            ->name('store');

        Route::get('/{sale}', [SalesController::class, 'show'])
            ->name('show');

        Route::get('/{sale}/edit', [SalesController::class, 'edit'])
            ->name('edit');

        Route::delete('/{sale}', [SalesController::class, 'destroy'])
            ->name('destroy');

        // API Routes
        Route::get('/search-products', [SalesController::class, 'searchProducts'])
            ->name('searchProducts');

        // 📱 BARCODE SCANNER - Main route
        Route::post('/scan-barcode', [SalesController::class, 'scanBarcode'])
            ->name('scanBarcode');

        Route::get('/statistics', [SalesController::class, 'statistics'])
            ->name('statistics');

        Route::get('/{sale}/print', [SalesController::class, 'printReceipt'])
            ->name('printReceipt');
    });

});
