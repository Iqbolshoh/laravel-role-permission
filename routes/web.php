<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

Auth::routes();


Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware(['admin'])->group(function () {
        Route::get('/admin', fn() => view('admin.dashboard'));
    });

    Route::middleware(['teacher'])->group(function () {
        Route::get('/teacher', fn() => view('teacher.dashboard'));
    });

    Route::middleware(['student'])->group(function () {
        Route::get('/student', fn() => view('student.dashboard'));
    });
});