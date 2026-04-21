<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\WarehouseController;
Route::middleware('auth.user')->group(function () {

    Route::prefix('warehouse')->name('warehouse.')->group(function () {

        // Dashboard
        Route::get('/', [WarehouseController::class, 'index'])
            ->name('index');

        // STOCK IN (KIRIM)
        Route::get('/stock-in/create', [WarehouseController::class, 'createStockIn'])
            ->name('stockInCreate');

        Route::post('/stock-in/store', [WarehouseController::class, 'storeStockIn'])
            ->name('stockInStore');

        Route::get('/stock-in/{stockIn}', [WarehouseController::class, 'showStockIn'])
            ->name('stockInShow');

        Route::get('/stock-in-history', [WarehouseController::class, 'stockInHistory'])
            ->name('stockInHistory');

        // STOCK OUT (CHIQIM)
        Route::get('/stock-out/create', [WarehouseController::class, 'createStockOut'])
            ->name('stockOutCreate');

        Route::post('/stock-out/store', [WarehouseController::class, 'storeStockOut'])
            ->name('stockOutStore');

        Route::get('/stock-out/{stockOut}', [WarehouseController::class, 'showStockOut'])
            ->name('stockOutShow');

        Route::get('/stock-out-history', [WarehouseController::class, 'stockOutHistory'])
            ->name('stockOutHistory');

        // SUPPLIERS (TA'MINOTCHILAR)
        Route::get('/suppliers', [WarehouseController::class, 'suppliers'])
            ->name('suppliers');

        Route::get('/suppliers/create', [WarehouseController::class, 'createSupplier'])
            ->name('supplierCreate');

        Route::post('/suppliers/store', [WarehouseController::class, 'storeSupplier'])
            ->name('supplierStore');

        Route::get('/suppliers/{supplier}/edit', [WarehouseController::class, 'editSupplier'])
            ->name('supplierEdit');

        Route::put('/suppliers/{supplier}', [WarehouseController::class, 'updateSupplier'])
            ->name('supplierUpdate');

        // STATISTICS API
        Route::get('/api/statistics', [WarehouseController::class, 'statistics'])
            ->name('statistics');
    });

});
