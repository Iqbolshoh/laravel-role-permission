<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages;

Route::middleware('auth')->group(function () {
    Route::post('/role/create', [Pages\CreateRole::class, 'create'])->name('filament.pages.create-role');
    Route::get('/role/{role}/edit', Pages\ManageRoles::class)->middleware('permission:role.edit');
    Route::delete('/role/{role}/delete', [Pages\ManageRoles::class, 'deleteRole'])->middleware('permission:role.delete');
});