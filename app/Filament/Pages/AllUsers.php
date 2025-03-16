<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AllUsers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.all-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 2;
}
