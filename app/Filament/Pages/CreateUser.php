<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CreateUser extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static string $view = 'filament.pages.create-user';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 1;
}
