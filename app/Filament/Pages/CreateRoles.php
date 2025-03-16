<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CreateRoles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static string $view = 'filament.pages.create-roles';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;
}
