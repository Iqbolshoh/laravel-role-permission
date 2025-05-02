<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\Auth\Register;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/register', Register::class)->name('register');
