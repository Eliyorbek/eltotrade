<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\ReportsController;

Route::middleware('auth.user')->group(function () {

    Route::prefix('reports')->name('reports.')->group(function () {
        // Dashboard
        Route::get('/', [ReportsController::class, 'index'])
            ->name('index');

        // Daily Reports
        Route::get('/daily', [ReportsController::class, 'daily'])
            ->name('daily');

        // Monthly Reports
        Route::get('/monthly', [ReportsController::class, 'monthly'])
            ->name('monthly');

        // Top Products
        Route::get('/top-products', [ReportsController::class, 'topProducts'])
            ->name('topProducts');

        // Profit Analysis
        Route::get('/profit', [ReportsController::class, 'profit'])
            ->name('profit');

        // Low Profit Products
        Route::get('/low-profit', [ReportsController::class, 'lowProfit'])
            ->name('lowProfit');

        // API - Chart Data
        Route::get('/api/chart-data', [ReportsController::class, 'chartData'])
            ->name('chartData');

        // Export
        Route::get('/export', [ReportsController::class, 'export'])
            ->name('export');

        Route::get('/export-pdf', [ReportsController::class, 'exportPdf'])
            ->name('exportPdf');
    });
});


