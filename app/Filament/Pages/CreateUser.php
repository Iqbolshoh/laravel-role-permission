<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Helpers\Utils;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class CreateUser extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static string $view = 'filament.pages.create-user';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 3;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public ?string $role = null; // Bitta role tanlash uchun string sifatida saqlanadi

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.create');
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('User Information')
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

                    TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required()
                        ->same('password'),
                ]),

            Section::make('Role')
                ->schema([
                    Select::make('role')
                        ->label('Select Role')
                        ->options(Role::pluck('name', 'name')->toArray())
                        ->required(),
                ]),
        ];
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
            'role' => 'required|exists:roles,name',
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

        $user->syncRoles([$this->role]);

        Utils::notify(
            'Success',
            "User '{$this->name}' created successfully with role '{$this->role}'!",
            'success'
        );

        $this->reset('name', 'email', 'password', 'password_confirmation', 'role');
    }
}
