<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 3;

    /**
     * Access Control: Determines if the user can access this page.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view') ?? false;
    }

    /**
     * Form Configuration: Defines fields for user creation and editing.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->disabled(fn($record) => $record?->hasRole('superadmin') || auth()->id() === $record?->id || !auth()->user()?->can('user.edit') || self::hasMatchingRoles($record)),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(User::class, 'email', ignoreRecord: true)
                ->disabled(fn($record) => $record?->hasRole('superadmin') || auth()->id() === $record?->id || !auth()->user()?->can('user.edit') || self::hasMatchingRoles($record)),

            TextInput::make('password')
                ->password()
                ->label('Password')
                ->minLength(8)
                ->maxLength(255)
                ->requiredWith('passwordConfirmation')
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->disabled(fn($record) => $record?->hasRole('superadmin') || auth()->id() === $record?->id || !auth()->user()?->can('user.edit') || self::hasMatchingRoles($record)),

            TextInput::make('passwordConfirmation')
                ->password()
                ->label('Confirm Password')
                ->minLength(8)
                ->maxLength(255)
                ->requiredWith('password')
                ->same('password')
                ->dehydrated(fn(?string $state): bool => filled($state))
                ->disabled(fn($record) => $record?->hasRole('superadmin') || auth()->id() === $record?->id || !auth()->user()?->can('user.edit') || self::hasMatchingRoles($record)),

            Select::make('roles')
                ->relationship('roles', 'name')
                ->preload()
                ->multiple()
                ->searchable()
                ->minItems(1)
                ->options(fn() => Role::where('name', '!=', 'superadmin')->pluck('name', 'id'))
                ->disabled(fn($record) => $record?->hasRole('superadmin') || auth()->id() === $record?->id || !auth()->user()?->can('user.edit') || self::hasMatchingRoles($record)),
        ]);
    }

    /**
     * Table Configuration: Configures the table for displaying users.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('roles.name')->searchable()->sortable()->badge(),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Created At')->toggleable()->toggledHiddenByDefault(),
                TextColumn::make('updated_at')->dateTime()->sortable()->label('Updated At')->toggleable()->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) => $record && !$record->hasRole('superadmin') && auth()->user()?->can('user.edit') && auth()->id() !== $record->id && !self::hasMatchingRoles($record)),
                Tables\Actions\DeleteAction::make()->visible(fn($record) => $record && !$record->hasRole('superadmin') && auth()->user()?->can('user.delete') && auth()->id() !== $record->id && !self::hasMatchingRoles($record)),
            ])
            ->bulkActions([]);
    }

    /**
     * Check if the authenticated user has any matching roles with the target user.
     */
    public static function hasMatchingRoles($record): bool
    {
        $authUserRoles = auth()->user()?->roles->pluck('name')->toArray() ?? [];
        $targetUserRoles = $record?->roles->pluck('name')->toArray() ?? [];
        return !empty(array_intersect($authUserRoles, $targetUserRoles));
    }

    /**
     * Relations Configuration: No related models defined.
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Page Routes Configuration: Defines routes for user management.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}