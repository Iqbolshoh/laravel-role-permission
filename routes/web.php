<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
Route::get('/', function () {
    
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin uchun marshrutlar (prefix bilan)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
});

// O‘qituvchilar uchun marshrutlar (prefix bilan)
Route::prefix('teacher')->middleware(['auth', 'role:teacher'])->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('teacher.dashboard');
});

// Talabalar uchun marshrutlar (prefix bilan)
Route::prefix('student')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('student.dashboard');
});


require __DIR__.'/auth.php';
