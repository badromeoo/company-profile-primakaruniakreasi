<?php

use App\Http\Controllers\Admin\HomeController as AdminHomeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

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
