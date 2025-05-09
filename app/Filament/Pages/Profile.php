<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];

    /**
     * Access Control: Determines if the user can access this page.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('profile.view');
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
                            ->disabled(fn() => !auth()->user()?->can('profile.edit')),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignorable: Auth::user())
                            ->maxLength(255)
                            ->disabled(fn() => !auth()->user()?->can('profile.edit')),
                    ])
                    ->collapsible(),

                Section::make('Change Password')
                    ->description('Change your current password securely.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required(fn($get) => filled($get('password')))
                            ->helperText('Enter your current password to change it.')
                            ->disabled(fn() => !auth()->user()?->can('profile.edit')),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->minLength(8)
                            ->helperText('Leave blank to keep your current password.')
                            ->reactive()
                            ->required(fn($get) => filled($get('current_password')))
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (empty($state)) {
                                    $set('password_confirmation', null);
                                    $set('current_password', null);
                                }
                            })
                            ->disabled(fn() => !auth()->user()?->can('profile.edit')),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required(fn($get) => filled($get('password')))
                            ->same('password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->disabled(fn() => !auth()->user()?->can('profile.edit')),

                    ])
                    ->collapsible(),

                Actions::make([
                    Action::make('save')
                        ->label('Save Changes')
                        ->action('save')
                        ->color('primary')
                        ->visible(fn() => auth()->user()?->can('profile.edit'))
                ])->fullWidth(),
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
        $data = $this->form->getState();

        if (!empty($data['password'])) {
            if (!Hash::check($data['current_password'] ?? '', Auth::user()->password)) {
                Notification::make()
                    ->title('Error')
                    ->body('Your current password is incorrect.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        Auth::user()->update($updateData);

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        Notification::make()
            ->title('Success')
            ->body('Your profile has been updated successfully!')
            ->success()
            ->send();

        $this->redirect('/login');
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form($this->makeForm()),
        ];
    }
}
