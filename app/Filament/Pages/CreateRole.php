<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\{Role, Permission};
use Filament\Notifications\Notification;
use App\Helpers\Utils;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;

    public string $name = '';
    public array $selectedPermissions = [];
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
    | Component Initialization
    |--------------------------------------------------------------------------
    | Runs when the page is mounted. It loads all permissions and groups them
    | based on their prefixes.
    */
    public function mount()
    {
        $this->groupedPermissions = Permission::all()->groupBy(fn($perm) => explode('.', $perm->name)[0])->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | Create New Role
    |--------------------------------------------------------------------------
    | Handles the creation of a new role with selected permissions.
    | Validates user input and ensures role uniqueness.
    */
    public function create()
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->name)) {
            return Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
        }

        if (Role::where('name', $this->name)->exists()) {
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
            'name' => 'required|string|regex:/^[a-zA-Z0-9_]+$/|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        Role::create(['name' => $this->name])->syncPermissions($this->selectedPermissions);

        Utils::notify(
            'Role Created Successfully',
            "Role '{$this->name}' has been created successfully!",
            'success'
        );

        $this->dispatch('roleCreated');
        $this->reset('name', 'selectedPermissions');
    }
}
