<?php

use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\HomeController as FrontHomeController;
use App\Http\Controllers\Front\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', FrontHomeController::class)->name('home');
Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::feeds();

Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('category/{category:slug}', [BlogController::class, 'category'])->name('category');
    Route::get('preview/{post}', [BlogController::class, 'preview'])
        ->middleware(['auth', 'signed'])
        ->name('preview');
    Route::get('{post:slug}', [BlogController::class, 'show'])->name('show');
});

/*
 * Authentication, account management, and content administration live in the
 * Filament CMS panel. These redirects keep the legacy Breeze URLs working for
 * bookmarks, e-mails, and the framework's `auth` middleware redirect target.
 */
Route::redirect('login', '/admin/cms/login')->name('login');
Route::redirect('dashboard', '/admin/cms')->name('dashboard');
