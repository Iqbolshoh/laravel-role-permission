<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;

    public $roles;
    public $groupedPermissions = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.create');
    }

    public function mount()
    {
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        })->map(function ($group) {
            return $group->values();
        });

        $this->groupedPermissions = $permissions;
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $validated['name']]);
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        session()->flash('success', 'Role created successfully!');

        return redirect()->route('filament.pages.create-role');
    }
}
