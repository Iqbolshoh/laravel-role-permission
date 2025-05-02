<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;

Route::get('/login', Login::class)->name('filament.auth.login');
Route::get('/register', Register::class)->name('filament.auth.register');