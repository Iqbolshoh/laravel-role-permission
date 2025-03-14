<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin
    Route::prefix('admin')->group(function () {
        Route::get('/', fn() => view('welcome'))->name('home');
        Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Teacher
    Route::prefix('teacher')->group(function () {
        Route::get('/', fn() => view('welcome'))->name('home');
        Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Student
    Route::prefix('student')->group(function () {
        Route::get('/', fn() => view('welcome'))->name('home');
        Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__ . '/auth.php';
