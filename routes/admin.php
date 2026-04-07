<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::resource('products', ProductController::class)->except('show');

        Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])
            ->name('products.variants.store');
        Route::get('products/{product}/variants/{variant}/edit', [ProductVariantController::class, 'edit'])
            ->name('products.variants.edit');
        Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])
            ->name('products.variants.update');
        Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])
            ->name('products.variants.destroy');
    });
});
