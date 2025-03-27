<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ManageUsers extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 4;

    public $isOpen = false;
    public $isEditMode = false;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $roles;

    public function mount(): void
    {
        $this->roles = Role::whereNot('name', 'superadmin')->pluck('name', 'name')->toArray();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query()->whereNot('id', auth()->id()))
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('roles.name')->label('Role')->sortable(),
            ])
            ->actions([
                EditAction::make()->action(fn($record) => $this->edit($record)),
                DeleteAction::make(),
            ]);
    }

    public function edit($record): void
    {
        $this->isEditMode = true;
        $this->isOpen = true;
        $this->name = $record->name;
        $this->email = $record->email;
        $this->role = $record->roles->first()->name ?? '';
    }

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($this->isEditMode ? $this->email : null),
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = Hash::make($data['password']);
        }

        $user = User::updateOrCreate(
            ['email' => $data['email']],
            $userData
        );
        $user->syncRoles([$data['role']]);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role', 'isOpen', 'isEditMode']);
        $this->notify('success', "User '{$user->name}' saved successfully!");
    }
}