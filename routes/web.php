<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function () {
    return 'Admin panel!';
})->middleware('role:admin');

Route::get('/blog', function () {
    return 'Blog sahifasi!';
})->middleware('permission:blog.view');

Route::get('/profile', function () {
    return 'Profile sahifasi!';
})->middleware('role_or_permission:admin|profile.edit');
