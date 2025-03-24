<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;

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

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    | Determines if the authenticated user has permission to access this page.
    */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('role.view');
    }

    /*
    |--------------------------------------------------------------------------
    | Component Initialization
    |--------------------------------------------------------------------------
    | Loads existing roles and permissions when the page is mounted.
    */
    public function mount()
    {
        $this->loadRoles();
        $this->permissions = Permission::all()->toArray();
        $this->groupPermissions();
    }

    /*
    |--------------------------------------------------------------------------
    | Load Roles
    |--------------------------------------------------------------------------
    | Fetches all roles except 'superadmin' and includes their assigned permissions.
    */
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

    /*
    |--------------------------------------------------------------------------
    | Group Permissions
    |--------------------------------------------------------------------------
    | Groups permissions by their category (prefix before the dot).
    */
    private function groupPermissions()
    {
        $this->groupedPermissions = collect($this->permissions)->groupBy(function ($permission) {
            return explode('.', $permission['name'])[0];
        })->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Role
    |--------------------------------------------------------------------------
    | Loads role details for editing, including assigned permissions.
    */
    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->roleId = $role->id;
        $this->roleName = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->isEditing = true;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Role
    |--------------------------------------------------------------------------
    | Validates and updates an existing role with new details and permissions.
    */
    public function updateRole()
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->roleName)) {
            return Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
        }

        if (Role::where('name', $this->roleName)->where('id', '!=', $this->roleId)->exists()) {
            return Utils::notify(
                'Role Already Exists',
                "Role '{$this->name}' already exists! Please choose another name.",
                'danger'
            );
        }

        if (empty($this->selectedPermissions)) {
            return Utils::notify(
                'No Permissions Selected',
                'You must select at least one permission to create a role.',
                'warning'
            );
        }

        $this->validate([
            'roleName' => 'required|string|max:255',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        $role = Role::findOrFail($this->roleId);
        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->selectedPermissions);

        $this->isEditing = false;

        return Utils::notify(
            'Role Updated Successfully',
            "Role '{$this->name}' has been updated successfully!",
            'success'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Role
    |--------------------------------------------------------------------------
    | Removes a role from the system and updates the list dynamically.
    */
    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $roleName = $role->name;
        $role->delete();

        $this->roles = array_filter($this->roles, function ($role) use ($roleId) {
            return $role['id'] !== $roleId;
        });

        Utils::notify(
            'Role Deleted',
            "Role '{$roleName}' has been deleted successfully!"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Reset Form
    |--------------------------------------------------------------------------
    | Clears form inputs and resets the state after adding/updating a role.
    */
    private function resetForm()
    {
        $this->roleId = null;
        $this->roleName = '';
        $this->selectedPermissions = [];
        $this->isEditing = false;
    }
}
