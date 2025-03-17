<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateUser extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static string $view = 'filament.pages.create-user';
    protected static ?string $navigationGroup = 'User';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Auth::user()?->can('role.create');
    }
}
