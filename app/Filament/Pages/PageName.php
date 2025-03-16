<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;

class PageName extends Page
{
    protected static string $routePath = '/page-name'; // Sahifaning URL yo'li

    protected static ?int $navigationSort = -2; // Navigatsiyadagi tartibi

    /**
     * @var string
     */
    protected static string $view = 'filament.resources.user-resource.pages.page-name'; // View joyi

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('PageName'); // Sahifa nomi
    }

    public static function getNavigationIcon(): string|Htmlable|null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('heroicon-m-home'); // Ikonkaning belgisi
    }

    public static function getRoutePath(): string
    {
        return static::$routePath; // URL manzili
    }

    /**
     * @return array<class-string
     */
    public function getWidgets(): array
    {
        return Filament::getWidgets(); // Sahifadagi vidjetlar
    }

    /**
     * @return array<class-string
     */
    public function getVisibleWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getWidgets());
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int|string|array
    {
        return 2; // Shtatlar soni
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title ?? __('Page Title'); // Sahifa sarlavhasi
    }
}
