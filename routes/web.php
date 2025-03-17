<?php

use Illuminate\Support\Facades\Route;

Route::get('/admin', function () {
    return view('welcome');
})->middleware('permission:test.create');