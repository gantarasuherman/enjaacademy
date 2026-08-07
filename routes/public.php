<?php

declare(strict_types=1);

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ====================== PUBLIC PAGES ======================
Route::controller(PublicController::class)->group(function () {
    Route::get('/', 'home')->name('home');
    Route::get('/tentang', 'about')->name('about');
    Route::get('/kontak', 'contact')->name('contact');
    // Method name must not collide with a global class: with ext-intl loaded,
    // a bare 'locale' resolves to PHP's built-in Locale class and Laravel
    // rejects it as a route action.
    Route::get('/locale/{locale}', 'switchLocale')->name('locale.switch');
});
