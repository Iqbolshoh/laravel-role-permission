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
        $this->permissions = Permission::all()->toArray();
        $this->groupPermissions();
    }

    private function notify(string $title, string $message, string $type = 'success')
    {
        Notification::make()->title($title)->body($message)->{$type}()->send();
    }

    private function loadRoles()
    {
        $this->roles = Role::where('name', '!=', 'superadmin')->with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray(),
            ];
        })->toArray();
    }

    private function groupPermissions()
    {
        $this->groupedPermissions = collect($this->permissions)->groupBy(function ($permission) {
            return explode('.', $permission['name'])[0];
        })->toArray();
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
    }

    public function updateRole()
    {
        $this->validate([
            'roleName' => 'required|string|max:255',
        ]);

        $role = Role::findOrFail($this->roleId);
        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->selectedPermissions);

        $this->notify('Role Updated', 'The role has been successfully updated.');

        $this->resetForm();
        $this->loadRoles();
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->delete();

        $this->roles = array_filter($this->roles, function ($role) use ($roleId) {
            return $role['id'] !== $roleId;
        });

        $this->notify('Role Deleted', 'The role has been successfully deleted.');
    }


    private function resetForm()
    {
        $this->roleId = null;
        $this->roleName = '';
        $this->selectedPermissions = [];
        $this->isEditing = false;
    }
}