<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Spatie\Permission\Models\{Role, Permission};
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

    private function notify(string $title, string $message, string $type = 'success')
    {
        Notification::make()->title($title)->body($message)->{$type}()->send();
    }

    public function create()
    {
        if (Role::where('name', $this->name)->exists()) {
            return $this->notify('Error', "Role '{$this->name}' already exists!", 'danger');
        }
        if (empty($this->selectedPermissions)) {
            return $this->notify('Warning', 'You must select at least one permission!', 'warning');
        }

        $this->validate([
            'name' => 'required|string|unique:roles,name',
            'selectedPermissions' => 'required|array|min:1',
        ]);

        Role::create(['name' => $this->name])->syncPermissions($this->selectedPermissions);

        $this->notify('Success', "Role '{$this->name}' created successfully!");
        $this->dispatch('roleCreated');
        $this->reset('name', 'selectedPermissions');
    }
}
