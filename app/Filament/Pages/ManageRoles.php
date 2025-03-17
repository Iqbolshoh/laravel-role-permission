<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Component;
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
    public $roleId, $roleName;
    public bool $isEditing = false;

    public function mount()
    {
        $this->loadRoles();
        $this->permissions = Permission::all()->pluck('name')->toArray();
    }

    public function loadRoles()
    {
        $this->roles = Role::with('permissions')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'permissions' => $role->permissions->pluck('name')->toArray()
            ];
        })->toArray();
    }

    public function editRole($roleId)
    {
        $role = Role::find($roleId);
        if ($role) {
            $this->roleId = $role->id;
            $this->roleName = $role->name;
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
            $this->isEditing = true;
        }
    }

    public function updateRole()
    {
        if ($this->roleId) {
            $role = Role::find($this->roleId);
            if ($role) {
                $role->name = $this->roleName;
                $role->syncPermissions($this->selectedPermissions);
                $role->save();

                Notification::make()
                    ->title('Success')
                    ->body('Role updated successfully with permissions!')
                    ->success()
                    ->send();

                $this->isEditing = false;
                $this->loadRoles();
            }
        }
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.view');
    }
}