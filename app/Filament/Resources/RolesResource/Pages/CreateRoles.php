<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRoles extends CreateRecord
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
     * Process form data before creating the role.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);
        $this->form->getState()['permissions'] = $permissionIds; // Store for afterCreate
        return $data;
    }

    /**
     * Sync permissions after creating the role.
     */
    protected function afterCreate(): void
    {
        $permissionIds = $this->form->getState()['permissions'] ?? [];
        RolesResource::syncPermissions($this->record, $permissionIds);
    }
}