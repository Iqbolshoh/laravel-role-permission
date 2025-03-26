<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\{Role, Permission};
use App\Helpers\Utils;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;

    public string $roleName = '';
    public array $permissions = [];
    public array $groupedPermissions = [];

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    | Determines if the authenticated user has permission to access this page.
    */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Mount Method
    |--------------------------------------------------------------------------
    | Fetches all available permissions and groups them by their prefix.
    | This method is executed when the component is initialized.
    */
    public function mount()
    {
        $this->groupedPermissions = Permission::all()->groupBy(fn($perm) => explode('.', $perm->name)[0])->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Save Method
    |--------------------------------------------------------------------------
    | Handles the creation of a new role with assigned permissions.
    | It validates input data, checks for duplicates, and saves the role.
    */
    public function save()
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->roleName)) {
            return Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
        }

        if (Role::where('name', $this->roleName)->exists()) {
            return Utils::notify(
                'Role Already Exists',
                "Role '{$this->roleName}' already exists! Please choose another name.",
                'danger'
            );
        }

        if (empty($this->permissions)) {
            return Utils::notify(
                'No Permissions Selected',
                'You must select at least one permission to create a role.',
                'warning'
            );
        }

        Role::create(['name' => $this->roleName])->syncPermissions($this->permissions);
        Utils::notify('Success', "Role '{$this->roleName}' created!", 'success');

        $this->dispatch('roleCreated');
        $this->reset('roleName', 'permissions');
    }
}
