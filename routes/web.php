<?php

use App\Livewire\Admin;
use App\Livewire\Auth\Login;
use App\Livewire\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', HomeController::class)->name('home');
Route::view('/tentang-kami', 'pages.about')->name('about');
Route::view('/program', 'pages.programs.index')->name('programs.index');
Route::get('/program/{slug}', fn ($slug) => view('pages.programs.show', compact('slug')))->name('programs.show');
Route::view('/berita', 'pages.news.index')->name('news.index');
Route::get('/berita/{slug}', fn ($slug) => view('pages.news.show', compact('slug')))->name('news.show');
Route::view('/tim-guru', 'pages.teachers')->name('teachers.index');
Route::view('/kontak', 'pages.contact')->name('contact');
Route::view('/ppdb', 'pages.ppdb')->name('ppdb.create');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home');
})->middleware('auth')->name('logout');

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');
    Route::get('/programs', Admin\Programs::class)->name('programs');
    Route::get('/news', Admin\News::class)->name('news');
    Route::get('/teachers', Admin\Teachers::class)->name('teachers');
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
