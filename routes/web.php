<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\HomeController as FrontHomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', FrontHomeController::class)->name('home');

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('category/{category:slug}', [BlogController::class, 'category'])->name('category');
    Route::get('preview/{post}', [BlogController::class, 'preview'])
        ->middleware(['auth', 'signed'])
        ->name('preview');
    Route::get('{post:slug}', [BlogController::class, 'show'])->name('show');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('home-content', [AdminHomeController::class, 'edit'])->name('home-content.edit');
    Route::put('home-content', [AdminHomeController::class, 'update'])->name('home-content.update');
});

require __DIR__.'/auth.php';
