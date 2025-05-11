<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoles extends EditRecord
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
            Actions\DeleteAction::make()->visible(fn($record) => $record->name !== 'superadmin'),
        ];
    }
}
