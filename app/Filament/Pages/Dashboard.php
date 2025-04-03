<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $slug = '/';

    protected function getViewData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'role' => optional($user->roles->first())->name,
            'joined' => $user->created_at->format('d M, Y'),
            'lastLogin' => $user->last_login_at ? $user->last_login_at->format('d M, Y H:i') : 'Not available',
        ];
    }
}