<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Profile Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(!$this->canEdit()),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignorable: auth()->user())
                            ->maxLength(255)
                            ->disabled(!$this->canEdit()),

                        TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->minLength(8)
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->disabled(!$this->canEdit()),

                        TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->same('password')
                            ->dehydrated(fn(?string $state): bool => filled($state))
                            ->disabled(!$this->canEdit()),
                    ])
            ])
            ->statePath('data')
            ->model(auth()->user());
    }

    public function save()
    {
        if (!$this->canEdit()) {
            Notification::make()
                ->title('Permission Denied')
                ->body('You do not have permission to edit your profile.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        auth()->user()->update($updateData);

        Notification::make()
            ->title('Success')
            ->body('Your profile has been updated successfully!')
            ->success()
            ->send();
    }

    public function canEdit(): bool
    {
        return auth()->check();
    }

    public function canDelete(): bool
    {
        return auth()->check();
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form($this->makeForm()),
        ];
    }
}
