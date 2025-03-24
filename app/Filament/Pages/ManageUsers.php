<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;
use Filament\Notifications\Notification;

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
        $this->users = User::with('roles')->latest()->get();
    }

    protected function getViewData(): array
    {
        return [
            'users' => $this->users,
            'roles' => Role::pluck('name')->toArray(),
        ];
    }


    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole($this->role);

        session()->flash('message', 'User created successfully.');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->userId = null;
        $this->isEditMode = false;
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        $user->delete();

        $this->users = User::with('roles')->latest()->get();

        Notification::make()
            ->title('User Deleted')
            ->body('The user has been successfully deleted.')
            ->send();
    }
}