<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageRoles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.manage-roles';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.view');
    }
}
