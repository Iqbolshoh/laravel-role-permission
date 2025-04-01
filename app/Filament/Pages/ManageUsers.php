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
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Utils; // Assuming this is where Utils is defined

class ManageUsers extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static string $view = 'filament.pages.manage-users';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 2;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = '';

    // Page access control (user.view)
    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    // Check if user can edit a user record (user.edit)
    public function canEdit(User $record): bool
    {
        return auth()->check() && auth()->user()->can('user.edit');
    }

    // Check if user can delete a user record (user.delete)
    public function canDelete(User $record): bool
    {
        return auth()->check() && auth()->user()->can('user.delete');
    }

    protected function getTableQuery()
    {
        return User::query();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('name')->label('Name')->searchable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('created_at')->label('Created At')->dateTime(),
        ];
    }

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

    protected function getTableBulkActions(): array
    {
        $bulkActions = [];

        // Only show bulk delete if user has user.delete permission
        if ($this->canDelete(new User())) { // Using a new User instance as a placeholder
            $bulkActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn() => $this->canDelete(new User())),
            ]);
        }

        return $bulkActions;
    }

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

    public function create()
    {
        $validatedData = $this->form->getState();

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $user->assignRole($validatedData['role']);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role']);
        Utils::notify('Success', 'User created successfully!', 'success');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
                ->statePath('data'),
        ];
    }
}