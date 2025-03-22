<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\CreateRole;
use Spatie\Permission\Models\Role;
use App\Filament\Pages;

Route::middleware('auth')->group(function () {
    Route::post('/role/create', [CreateRole::class, 'create'])->name('filament.pages.create-role');
    Route::get('/role/{role}/edit', Pages\ManageRoles::class)->middleware('permission:role.edit');
    Route::delete('/role/{role}', function (Role $role) {
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!',
        ]);
    })->middleware('permission:role.delete');
});