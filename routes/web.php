<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| This file defines all web routes for the application.
| Only authenticated users can access these routes.
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    | Routes for creating, editing, and deleting roles.
    | Access is restricted based on user permissions.
    */

    Route::prefix('role')->group(function () {
        Route::post('create', [Pages\CreateRole::class, 'create'])->middleware('permission:role.create');
        Route::get('{role}/edit', Pages\ManageRoles::class)->middleware('permission:role.edit');
        Route::delete('{role}/delete', [Pages\ManageRoles::class, 'deleteRole'])->middleware('permission:role.delete');
    });

});
