<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;

    public string $name = '';
    public array $selectedPermissions = [];
    public array $groupedPermissions = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.create');
    }

    public function mount()
    {
        $permissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        })->map(function ($group) {
            return $group->toArray();
        })->toArray();

        $this->groupedPermissions = $permissions;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|unique:roles,name',
            'selectedPermissions' => 'array',
        ]);

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($this->selectedPermissions);

        Notification::make()
            ->title('Success')
            ->body('Role created successfully!')
            ->success()
            ->send();

        $this->dispatch('roleCreated');
        $this->reset('name', 'selectedPermissions');
    }
}
