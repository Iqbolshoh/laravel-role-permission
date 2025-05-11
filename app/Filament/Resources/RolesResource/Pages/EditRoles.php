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

    /**
     * Process form data before saving the role.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $permissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);
        $this->form->getState()['permissions'] = $permissionIds; // Store for afterSave
        return $data;
    }

    /**
     * Sync permissions after saving the role.
     */
    protected function afterSave(): void
    {
        $permissionIds = $this->form->getState()['permissions'] ?? [];
        RolesResource::syncPermissions($this->record, $permissionIds);
    }
}