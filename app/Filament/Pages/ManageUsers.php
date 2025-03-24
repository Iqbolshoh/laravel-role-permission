<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;
use Illuminate\Support\Facades\Validator;

class ManageUsers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 4;

    public $name, $email, $password, $password_confirmation, $role, $userId;
    public $isEditMode = false;
    public $users;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    public function mount()
    {
        $this->users = User::with('roles')
            ->whereNot('id', auth()->id())
            ->latest()
            ->get();

        $this->roles = Role::whereNot('name', 'superadmin')->pluck('name')->toArray();
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
        $validator = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'role' => $this->role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $field => $errors) {
                foreach ($errors as $error) {
                    Utils::notify(
                        'Error',
                        ucfirst($field) . ': ' . $error,
                        'danger'
                    );
                }
            }
            return;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            Utils::notify(
                'Error',
                'Email: Invalid email format!',
                'danger'
            );
            return;
        }

        $user = User::findOrFail($this->userId);
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (!empty($this->password)) {
            $updateData['password'] = Hash::make($this->password);
        }

        $user->update($updateData);
        $user->syncRoles([$this->role]);
        
        $this->users = User::with('roles')->latest()->get();

        Utils::notify(
            'Success',
            "User '{$this->name}' updated successfully with role '{$this->role}'!",
            'success'
        );

        $this->resetForm();
        $this->dispatch('closeModal');
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'role', 'userId', 'isEditMode']);
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
