<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
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
        $this->groupedPermissions = Permission::all()->groupBy(fn($perm) => explode('.', $perm->name)[0])->toArray();
    }

    public function checkRoleName()
    {
        if (Role::where('name', $this->name)->exists()) {
            Notification::make()
                ->title('Error')
                ->body('This role name already exists!')
                ->danger()
                ->send();

            return false;
        }
        return true;
    }

    public function save()
    {
        if (!$this->checkRoleName()) {
            return;
        }

        $this->validate([
            'name' => 'required|string|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
        ], [
            'name.unique' => 'This role name already exists!',
            'selectedPermissions.required' => 'At least one permission must be selected!',
            'selectedPermissions.min' => 'At least one permission must be selected!',
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