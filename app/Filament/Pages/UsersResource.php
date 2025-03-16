<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class UsersResource extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.users-resource';
}
