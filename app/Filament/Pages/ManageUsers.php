<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageUsers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'User';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }
}
