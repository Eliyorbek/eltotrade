<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;


Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// routes/web.php

Route::middleware('auth.user')->group(function () {
    Route::get('/', fn () => view('backend.index'))->name('home');

    // Employees
    Route::resource('employees', \App\Http\Controllers\Backend\EmployeeController::class)->except(['show']);
    Route::resource('categories', \App\Http\Controllers\Backend\CategoryController::class)->except(['show']);

    Route::resource('products', \App\Http\Controllers\Backend\ProductController::class);

    // Barcode skaner API
    Route::get('products/barcode/find', [\App\Http\Controllers\Backend\ProductController::class, 'findByBarcode'])
        ->name('products.barcode.find');


});

require "sales.php";
