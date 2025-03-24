<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;

class ManageUsers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 4;

    public $name, $email, $password, $role, $userId;
    public $isEditMode = false;
    public $users;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    public function mount()
    {
        $this->users = User::with('roles')
            ->where('id', '!=', auth()->id())
            ->latest()
            ->get();

        $this->roles = Role::where('name', '!=', 'superadmin')->with('permissions')->get();
    }

    protected function getViewData(): array
    {
        return [
            'users' => $this->users,
            'roles' => Role::pluck('name')->toArray(),
        ];
    }

    public function editUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->roles->pluck('name')->first();
        $this->isEditMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->role)) {
            return Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
        }

        $user = User::findOrFail($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $user->syncRoles([$this->role]);

        Utils::notify('Success', 'User updated successfully.', 'success');

        $this->resetForm();

        $this->dispatch('closeModal'); 

        return;
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'role', 'isEditMode']);
    }


    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);
        $userName = $user->name;
        $user->delete();

        $this->users = User::with('roles')->latest()->get();

        Utils::notify(
            'User Deleted',
            "User '{$userName}' has been successfully deleted!",
            'success'
        );
    }
}
