<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsersResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 3;

    /**
     * Access Control: Only users with 'user.view' permission can access this resource.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('user.view');
    }

    /**
     * Define the form used for creating and editing users.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->password()
                ->label('Password')
                ->required(fn(string $context) => $context === 'create')
                ->dehydrated(fn($state) => filled($state))
                ->maxLength(255)
                ->same('passwordConfirmation'),

            TextInput::make('passwordConfirmation')
                ->password()
                ->label('Confirm Password')
                ->required(fn(string $context) => $context === 'create')
                ->dehydrated(false),

            Select::make('role')
                ->label('Select Role')
                ->options(function () {
                    $roles = Role::pluck('name', 'name')->toArray();

                    if (User::role('superadmin')->count() >= 1) {
                        unset($roles['superadmin']);
                    }

                    return $roles;
                })

                ->required()
                ->searchable()
                ->reactive()
                ->default(fn($record) => $record?->roles?->first()?->name ?? null)
                ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                ->afterStateUpdated(function ($state, $record) {
                    if ($state && $record) {
                        $record->syncRoles([$state]);
                    }
                })
                ->afterStateHydrated(function (Select $component, $record) {
                    if ($record) {
                        $component->state($record->roles->first()?->name);
                    }
                })
                ->disabled(fn($record) => $record && $record->hasRole('superadmin'))
        ]);
    }

    /**
     * Define the table that lists users.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->label('ID'),
                Tables\Columns\TextColumn::make('name')->sortable()->label('Name'),
                Tables\Columns\TextColumn::make('email')->sortable()->label('Email'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->sortable()
                    ->label('Role')
                    ->badge()
                    ->getStateUsing(fn($record) => $record->roles->pluck('name')->implode(', ')),
                Tables\Columns\TextColumn::make('created_at')->sortable()->label('Created')->dateTime(),
            ])

            ->filters([])

            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->hasRole('superadmin') && auth()->user()?->can('user.edit')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => !$record->hasRole('superadmin') && auth()->user()?->can('user.delete')),
            ])

            ->bulkActions([]);
    }

    /**
     * Define any relationships (none for now).
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Define the pages associated with this resource.
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
