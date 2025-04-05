<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as FilamentAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Utils;

class ManageUsers extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 5;

    /*
    |--------------------------------------------------------------------------
    | Access Control Check
    |--------------------------------------------------------------------------
    | Determines if the authenticated user has permission to access this page
    */
    public static function canAccess(string $permission = 'view'): bool
    {
        if (!$user = auth()->user())
            return false;

        return match ($permission) {
            'view' => $user->can('user.view'),
            'create' => $user->can('user.create'),
            'edit' => $user->can('user.edit'),
            'delete' => $user->can('user.delete'),
            default => false,
        };
    }

    /*
    |---------------------------------------------------------------------- 
    | Table Query Definition
    |---------------------------------------------------------------------- 
    | Retrieves the base query for the users table.
    */
    protected function getTableQuery()
    {
        return User::query();
    }

    /*
    |---------------------------------------------------------------------- 
    | Table Columns Definition
    |---------------------------------------------------------------------- 
    | Defines the columns to be displayed in the users table.
    */
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('name')->label('Name')->sortable()->searchable(),
            TextColumn::make('email')->label('Email')->sortable()->searchable(),
            TextColumn::make('roles.name')->label('Role')->sortable()->searchable(),
            TextColumn::make('created_at')->label('Created At')->sortable()->dateTime(),
            TextColumn::make('updated_at')->label('Updated At')->sortable()->dateTime(),
        ];
    }

    /*
    |---------------------------------------------------------------------- 
    | Table Actions Definition
    |---------------------------------------------------------------------- 
    | Defines the actions available for each user record in the table.
    */
    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->visible(function (User $record) {
                    return $this->canAccess('edit') && (
                        !$record->hasRole('superadmin') || auth()->user()->hasRole('superadmin')
                    );
                })
                ->form($this->getEditFormSchema())
                ->mutateRecordDataUsing(function (array $data, User $record): array {
                    $data['role'] = $record->roles->first()?->name;
                    return $data;
                })
                ->action(function (User $record, array $data): void {
                    $this->updateUser($record, $data);
                }),

            DeleteAction::make()
                ->visible(function (User $record) {
                    return $this->canAccess('delete') && (
                        !$record->hasRole('superadmin') || auth()->user()->hasRole('superadmin')
                    );
                })
                ->before(function (User $record) {
                    if ($record->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
                        Utils::notify('Error', 'You cannot delete a Superadmin!', 'error');
                        return false;
                    }
                }),
        ];
    }

    /*
    |---------------------------------------------------------------------- 
    | Table Bulk Actions Definition
    |---------------------------------------------------------------------- 
    | Defines the bulk actions available for selected user records.
    */
    protected function getTableBulkActions(): array
    {
        return $this->canAccess('delete') ? [
            BulkActionGroup::make([DeleteBulkAction::make()]),
        ] : [];
    }

    /*
    |---------------------------------------------------------------------- 
    | User Creation Form Schema
    |---------------------------------------------------------------------- 
    | Defines the form schema for creating a new user.
    */
    protected function getCreateFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique('users', 'email', ignorable: fn(?User $record) => null),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->minLength(8)
                ->required(),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->same('password')
                ->required(),

            Select::make('role')
                ->label('Select Role')
                ->options(fn() => \Spatie\Permission\Models\Role::pluck('name', 'name')->toArray())
                ->required()
                ->searchable(),
        ];
    }

    /*
    |---------------------------------------------------------------------- 
    | User Edit Form Schema
    |---------------------------------------------------------------------- 
    | Defines the form schema for editing an existing user.
    */
    protected function getEditFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique('users', 'email', ignorable: fn(?User $record) => $record),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->minLength(8)
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (empty($state)) {
                        $set('password_confirmation', null);
                    }
                }),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->required(fn($get) => filled($get('password')))
                ->same('password')
                ->dehydrated(fn(?string $state): bool => filled($state)),

            Select::make('role')
                ->label('Select Role')
                ->options(fn() => \Spatie\Permission\Models\Role::pluck('name', 'name')->toArray())
                ->required()
                ->searchable(),
        ];
    }

    /*
    |---------------------------------------------------------------------- 
    | Header Actions Definition
    |---------------------------------------------------------------------- 
    | Defines actions available in the header of the page, such as creating a new user.
    */
    protected function getHeaderActions(): array
    {
        return [
            FilamentAction::make('create')
                ->label('Create User')
                ->icon('heroicon-o-user-plus')
                ->form($this->getCreateFormSchema())
                ->action(function (array $data): void {
                    $this->createUser($data);
                })
                ->color('primary')
                ->visible(fn() => self::canAccess('create')),
        ];
    }

    /*
    |---------------------------------------------------------------------- 
    | Create User Method
    |---------------------------------------------------------------------- 
    | Creates a new user in the database.
    */
    private function createUser(array $data): void
    {
        if (!self::canAccess('create')) {
            Utils::notify('Error', 'You do not have permission to create a user.', 'error');
            return;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($data['role']);
        Utils::notify('Success', 'User created successfully!', 'success');
    }

    /*
    |---------------------------------------------------------------------- 
    | Update User Method
    |---------------------------------------------------------------------- 
    | Updates an existing user's data.
    */
    private function updateUser(User $record, array $data): void
    {
        if ($record->hasRole('superadmin') && !auth()->user()->hasRole('superadmin')) {
            Utils::notify('Error', 'You cannot update a Superadmin!', 'error');
            return;
        }

        $record->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? Hash::make($data['password']) : $record->password,
        ]);

        $record->syncRoles($data['role']);
        Utils::notify('Success', 'User updated successfully!', 'success');
    }
}
