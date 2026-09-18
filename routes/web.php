<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Customer Routes (Public)
Route::get('/table/{qr_token}', [\App\Http\Controllers\CustomerController::class, 'menu'])->name('customer.menu');
Route::post('/table/{qr_token}/order', [\App\Http\Controllers\CustomerController::class, 'placeOrder'])->name('customer.order');

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
    Route::post('tables/{table}/regenerate-qr', [\App\Http\Controllers\Admin\TableController::class, 'regenerateQr'])->name('tables.regenerate-qr');
    Route::get('cashier', [\App\Http\Controllers\Admin\CashierController::class, 'index'])->name('cashier.index');
    Route::post('cashier/{session}/checkout', [\App\Http\Controllers\Admin\CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::get('kitchen', [\App\Http\Controllers\Admin\KitchenController::class, 'index'])->name('kitchen.index');
    Route::patch('kitchen/{item}', [\App\Http\Controllers\Admin\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';
