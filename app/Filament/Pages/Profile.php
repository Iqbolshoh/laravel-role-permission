<?php

namespace App\Filament\Pages;

use App\Helpers\Utils;
use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return Auth::check() && (Auth::user()->can('profile.view') ?? false);
    }

    public function canEdit(): bool
    {
        return Auth::check() && Auth::user()->can('profile.edit');
    }

    public function canDelete(): bool
    {
        return Auth::check() && Auth::user()->can('profile.delete');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('My Profile')
                    ->description('Update your account details below.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn() => !$this->canEdit()),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignorable: Auth::user())
                            ->maxLength(255)
                            ->disabled(fn() => !$this->canEdit()),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->minLength(8)
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->disabled(fn() => !$this->canEdit())
                            ->helperText('Leave blank to keep your current password.'),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->same('password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->disabled(fn() => !$this->canEdit()),

                        Actions::make([
                            Action::make('save')
                                ->label('Save Changes')
                                ->action('save')
                                ->color('primary')
                                ->visible(fn() => $this->canEdit()),

                            Action::make('delete')
                                ->label('Delete Profile')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Are you sure?')
                                ->modalDescription('This action will permanently delete your profile.')
                                ->modalSubmitActionLabel('Yes, delete it')
                                ->action('delete')
                                ->visible(fn() => $this->canDelete()),
                        ])->fullWidth(),
                    ])
                    ->collapsible(),
            ])
            ->statePath('data')
            ->model(Auth::user());
    }

    public function mount(): void
    {
        if (Auth::check()) {
            $this->form->fill([
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ]);
        }
    }

    public function save(): void
    {
        if (!$this->canEdit()) {
            Utils::notify('Permission Denied', 'You do not have permission to edit your profile.', 'danger');
            return;
        }

        $this->validate([
            'data.name' => 'required|max:255',
            'data.email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'data.password' => 'nullable|min:8|confirmed',
        ]);

        $data = $this->form->getState();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        Auth::user()->update($updateData);

        Utils::notify('Success', 'Your profile has been updated successfully!', 'success');
    }

    public function delete(): void
    {
        if (!$this->canDelete()) {
            Utils::notify('Permission Denied', 'You do not have permission to delete your profile.', 'danger');
            return;
        }

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        Utils::notify('Profile Deleted', 'Your profile has been deleted successfully.', 'success');

        $this->redirect('/login');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form($this->makeForm()),
        ];
    }
}
