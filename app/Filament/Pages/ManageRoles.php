<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Notifications\Notification;

class ManageRoles extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.manage-roles';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 2;

    public array $roles = [];
    public array $permissions = [];
    public array $selectedPermissions = [];
    public array $groupedPermissions = [];
    public ?int $roleId = null;
    public string $roleName = '';
    public bool $isEditing = false;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('role.view');
    }

    public function mount()
    {
        $this->loadRoles();
        $this->permissions = Permission::pluck('name')->toArray();
        $this->groupPermissions();
    }

    private function loadRoles()
    {
        $this->roles = Role::with('permissions')->get()->map(fn($role) => [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $role->permissions->pluck('name')->toArray(),
        ])->toArray();
    }

    private function groupPermissions()
    {
        $this->groupedPermissions = Permission::all()->groupBy(fn($permission) => explode('.', $permission->name)[0] ?? 'Other')->toArray();
    }

    public function editRole(int $roleId)
    {
        $role = Role::find($roleId);
        if (!$role) {
            return $this->sendNotification('Error', 'Role not found!', 'danger');
        }

        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
    }

    public function updateRole()
    {
        if (!$this->roleId) {
            return $this->sendNotification('Error', 'No role selected!', 'danger');
        }

        $role = Role::find($this->roleId);
        if (!$role) {
            return $this->sendNotification('Error', 'Role not found!', 'danger');
        }

        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->selectedPermissions);

        $this->sendNotification('Success', 'Role updated successfully!', 'success');
        $this->isEditing = false;
        $this->loadRoles();
    }

    private function sendNotification(string $title, string $message, string $type)
    {
        Notification::make()
                    ->title($title)
                    ->body($message)
            ->{$type}()
                ->send();
    }
}