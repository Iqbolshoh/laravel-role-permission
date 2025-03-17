<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Pages\CreateRole;
use Spatie\Permission\Models\Role;

Route::post('/create-role', [CreateRole::class, 'save'])->name('filament.pages.create-role');