<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Permission;

class CreateRoles extends CreateRecord
{
    protected static string $resource = RolesResource::class;

    protected array $permissions = [];

    /**
     * Mutate the form data before creating the role.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Permissions from form (IDs)
        $this->permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    /**
     * After the role is created, sync its permissions.
     */
    protected function afterCreate(): void
    {
        if (!empty($this->permissions)) {
            // Convert IDs to permission names
            $permissionNames = Permission::whereIn('id', $this->permissions)->pluck('name')->toArray();
            $this->record->syncPermissions($permissionNames);
        }
    }
}
