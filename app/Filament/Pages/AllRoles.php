<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AllRoles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static string $view = 'filament.pages.user-roles';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 2;
}
