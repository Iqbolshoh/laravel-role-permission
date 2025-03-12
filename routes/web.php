<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

Auth::routes();


Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Admin
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::get('/', fn() => view('admin.dashboard'))->name('admin.dashboard');
        Route::get('/profile', fn() => view('admin.profile'))->name('admin.profile');
    });

    // Teacher
    Route::middleware(['teacher'])->prefix('teacher')->group(function () {
        Route::get('/', fn() => view('teacher.dashboard'))->name('teacher.dashboard');
        Route::get('/profile', fn() => view('teacher.profile'))->name('teacher.profile');
    });

    // Student
    Route::middleware(['student'])->prefix('student')->group(function () {
        Route::get('/', fn() => view('student.dashboard'))->name('student.dashboard');
        Route::get('/profile', fn() => view('student.profile'))->name('student.profile');
    });
});
