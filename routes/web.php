<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUDs de Recursos
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);

    // Perfil de usuario (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/ui/toggle-nav', function () {
    $current = session('nav_style', config('ui.nav_style', 'dock'));
    $new = $current === 'dock' ? 'sidebar' : 'dock';
    session(['nav_style' => $new]);
    return back();
})->name('ui.toggle-nav');

require __DIR__.'/auth.php';
