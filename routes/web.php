<?php

use App\Http\Controllers\SitemapController;
use App\Livewire\Admin;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Livewire\Auth\TwoFactorSettings;
use App\Livewire\Guru;
use App\Livewire\Public;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', Public\Home::class)->name('home');
Route::get('/tentang-kami', Public\About::class)->name('about');
Route::get('/program', Public\ProgramsIndex::class)->name('programs.index');
Route::get('/program/{slug}', Public\ProgramShow::class)->name('programs.show');
Route::get('/berita', Public\NewsIndex::class)->name('news.index');
Route::get('/berita/{slug}', Public\NewsShow::class)->name('news.show');
Route::get('/tim-guru', Public\Teachers::class)->name('teachers.index');
Route::get('/galeri/{slug}', Public\AlbumShow::class)->name('gallery.album');
Route::get('/kontak', Public\Contact::class)->name('contact');
Route::get('/ppdb', Public\Ppdb::class)->name('ppdb.create');

// SEO
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', function () {
    $body = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /guru\n\nSitemap: ".route('sitemap')."\n";

    return response($body, 200, ['Content-Type' => 'text/plain']);
})->name('robots');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    Route::get('/two-factor-challenge', TwoFactorChallenge::class)->name('two-factor.challenge');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('home');
})->middleware('auth')->name('logout');

// Account security (2FA) — available to any authenticated staff member.
Route::get('/two-factor', TwoFactorSettings::class)->middleware('auth')->name('two-factor.settings');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');
    Route::get('/programs', Admin\Programs::class)->name('programs');
    Route::get('/news', Admin\News::class)->name('news');
    Route::get('/teachers', Admin\Teachers::class)->name('teachers');
    Route::get('/brochures', Admin\Brochures::class)->name('brochures');
    Route::get('/gallery', Admin\Gallery::class)->name('gallery');
    Route::get('/gallery/{album}/photos', Admin\AlbumPhotos::class)->name('gallery.photos');
    Route::get('/ppdb', Admin\Ppdb::class)->name('ppdb');
    Route::get('/contacts', Admin\Contacts::class)->name('contacts');
    Route::get('/visits', Admin\Visits::class)->name('visits');
    Route::get('/users', Admin\Users::class)->name('users');
    Route::get('/icons', Admin\IconLibrary::class)->name('icons');
    Route::get('/settings', Admin\Settings::class)->name('settings');
});

// Guru
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/', Guru\Dashboard::class)->name('dashboard');
    Route::get('/news', Guru\MyNews::class)->name('news');
    Route::get('/profile', Guru\Profile::class)->name('profile');
});
