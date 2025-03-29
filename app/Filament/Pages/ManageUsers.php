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

    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.manage');
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
            ->form($this->getFormSchema())
            ->mutateRecordDataUsing(function (array $data, User $record): array {
                $data['role'] = $record->roles->first()?->name;
                return $data;
            }),
        DeleteAction::make(),
    ];
}

    protected function getTableBulkActions(): array
    {
        return [
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ];
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

            Select::make('role')
                ->label('Select Role')
                ->options(\Spatie\Permission\Models\Role::pluck('name', 'name')->toArray())
                ->required()
        ];
    }

    public function create()
    {
        $validatedData = $this->form->getState();

        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        $this->reset(['name', 'email', 'password', 'password_confirmation', 'role']);
        $this->notify('success', 'User created successfully!');
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