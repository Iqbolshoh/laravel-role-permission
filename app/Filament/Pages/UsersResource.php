<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UsersResource extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static string $view = 'filament.pages.users-resource';
}
