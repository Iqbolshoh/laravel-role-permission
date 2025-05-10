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

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if (!empty($this->permissions)) {
            $permissionNames = Permission::whereIn('id', $this->permissions)->pluck('name')->toArray();
            $this->record->syncPermissions($permissionNames);
        }
    }
}
