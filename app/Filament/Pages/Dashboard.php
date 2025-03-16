<?php

namespace Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Facades\FilamentIcon;
use Filament\Facades\Filament;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static string $routePath = '/dashboard';
    protected static ?int $navigationSort = -2;
}
