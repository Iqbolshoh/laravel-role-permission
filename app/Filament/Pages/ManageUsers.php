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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as FilamentAction;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Utils;
use Illuminate\Support\Facades\Validator;

class ManageUsers extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 5;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';

    /*
    |----------------------------------------------------------------------
    | Access Control
    |----------------------------------------------------------------------
    | Determines if the authenticated user has permission to access this page.
    */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    /*
    |----------------------------------------------------------------------
    | Crete Permission Check
    |----------------------------------------------------------------------
    | Checks if the authenticated user has permission to create a user record.
    */
    public static function canCreate(): bool
    {
        return auth()->user()?->can('user.create');
    }

    /*
    |----------------------------------------------------------------------
    | Edit Permission Check
    |----------------------------------------------------------------------
    | Checks if the authenticated user has permission to edit a user record.
    */
    public function canEdit(User $record): bool
    {
        return auth()->check() && auth()->user()->can('user.edit');
    }

    /*
    |----------------------------------------------------------------------
    | Delete Permission Check
    |----------------------------------------------------------------------
    | Checks if the authenticated user has permission to delete a user record.
    */
    public function canDelete(User $record): bool
    {
        return auth()->check() && auth()->user()->can('user.delete');
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
                ->visible(fn(User $record) => $this->canEdit($record))
                ->form($this->getFormSchema())
                ->mutateRecordDataUsing(function (array $data, User $record): array {
                    $data['role'] = $record->roles->first()?->name;
                    return $data;
                })
                ->action(function (User $record, array $data): void {
                    $record->update([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'password' => isset($data['password']) ? Hash::make($data['password']) : $record->password,
                    ]);
                    $record->syncRoles($data['role']);
                    Utils::notify('Success', 'User updated successfully!', 'success');
                }),

            DeleteAction::make()
                ->visible(fn(User $record) => $this->canDelete($record)),
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
        $bulkActions = [];

        if ($this->canDelete(new User())) {
            $bulkActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn() => $this->canDelete(new User())),
            ]);
        }

        return $bulkActions;
    }

    /*
    |----------------------------------------------------------------------
    | Form Schema Definition
    |----------------------------------------------------------------------
    | Defines the structure and fields of the user creation/edit form.
    */
    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email Address')
                ->email()
                ->unique('users', 'email', ignorable: fn(?User $record) => $record)
                ->required()
                ->maxLength(255),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->minLength(8)
                ->required(fn(string $context): bool => $context === 'create')
                ->dehydrated(fn(?string $state): bool => filled($state)),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->required(fn(string $context): bool => $context === 'create')
                ->same('password')
                ->dehydrated(fn(?string $state): bool => filled($state)),

            Select::make('role')
                ->label('Select Role')
                ->options(\Spatie\Permission\Models\Role::pluck('name', 'name')->toArray())
                ->required()
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Header Actions Definition
    |----------------------------------------------------------------------
    | Defines actions in the page header, including the create user form trigger.
    */
    protected function getHeaderActions(): array
    {
        return [
            FilamentAction::make('create')
                ->label('Create User')
                ->icon('heroicon-o-user-plus')
                ->form($this->getFormSchema())
                ->action(function (array $data): void {
                    if (!auth()->user()?->can('user.create')) {
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
                })
                ->color('primary')
                ->visible(fn() => auth()->user()?->can('user.create')),
        ];
    }

    /*
    |----------------------------------------------------------------------
    | Create New User
    |----------------------------------------------------------------------
    | Creates a new user with the provided data and assigns a role.
    */
    public function create()
    {
        $validatedData = $this->validateFormData($this->form->getState());

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->assignRole($validatedData['role']);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role']);

        Utils::notify('Success', 'User created successfully!', 'success');
    }

    /*
    |----------------------------------------------------------------------
    | Form Registration
    |----------------------------------------------------------------------
    | Registers the form instance for use within the page.
    */
    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data'),
        ];
    }
}