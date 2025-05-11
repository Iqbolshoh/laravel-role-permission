<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RolesResource::class;

    /**
     * Restrict access to superadmins only.
     */
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->hasRole('superadmin') ?? false;
    }

    /**
     * Define header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}