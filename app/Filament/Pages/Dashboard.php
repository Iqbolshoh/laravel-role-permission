<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?string $slug = '/';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('dashboard.view');
    }

    protected function getViewData(): array
    {
        $user = Auth::user();

        return [
            'name' => $user->name,
            'email' => $user->email,
            'role' => optional($user->roles->first())->name ?? 'User',
            'joined' => $user->created_at->format('d M, Y'),
        ];
    }
}
