<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\CreateRole;
use Spatie\Permission\Models\Role;
use App\Filament\Pages;

Route::middleware('auth')->group(function () {
    Route::post('/create-role', [CreateRole::class, 'save'])->name('filament.pages.create-role');

    Route::get('/roles/{role}/edit', Pages\ManageRoles::class)->middleware('permission:role.edit');

    Route::delete('/roles/{role}', function (Role $role) {
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!',
        ]);
    })->middleware('permission:role.delete');
});

Route::get('/admin', function () {
    return view('bootsrap');
});