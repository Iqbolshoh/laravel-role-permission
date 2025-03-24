<?php

namespace App\Filament\Pages;

use App\Helpers\Utils;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\{TextInput, Select};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateUser extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static string $view = 'filament.pages.create-user';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 3;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = ''; // Confirm Password
    public string $role = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.create');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->unique('users', 'email')
                    ->required()
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8),

                TextInput::make('password_confirmation') // Confirm Password
                    ->label('Confirm Password')
                    ->password()
                    ->required()
                    ->same('password'),

                Select::make('role')
                    ->label('User Role')
                    ->options(Role::pluck('name', 'name')->toArray())
                    ->required(),
            ])
            ->statePath('data');
    }

    public function create()
    {
        $validator = Validator::make([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'role' => $this->role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole($this->role);

        Utils::notify(
            'Success',
            "User '{$this->name}' created successfully with role '{$this->role}'!",
            'success'
        );

        $this->reset('name', 'email', 'password', 'password_confirmation', 'role');
    }
}
