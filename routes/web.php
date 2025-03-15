<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route::get('/admin', function () {
//     return 'Admin panel!';
// })->middleware('role:admin');

// Route::get('/blog', function () {
//     return 'Blog sahifasi!';
// })->middleware('permission:blog.view');

// Route::get('/profile', function () {
//     return 'Profile sahifasi!';
// })->middleware('role_or_permission:admin|profile.edit');


require __DIR__.'/auth.php';
