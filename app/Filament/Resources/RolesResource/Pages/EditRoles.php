<?php

namespace App\Filament\Resources\RolesResource\Pages;

use App\Filament\Resources\RolesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditRoles extends EditRecord
{
    protected static string $resource = RolesResource::class;

    protected array $permissions = [];

    /**
     * Mutate the form data before saving.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    /**
     * After updating the role, sync its permissions.
     */
    protected function afterSave(): void
    {
        if (!empty($this->permissions)) {
            $permissionNames = Permission::whereIn('id', $this->permissions)->pluck('name')->toArray();
            $this->record->syncPermissions($permissionNames);
        }
    }

    /**
     * Hide delete action for superadmin.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn($record) => $record->name !== 'superadmin'),
        ];
    }
}
