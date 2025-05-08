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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn($record) => auth()->user()->hasRole('superadmin') && $record->hasRole('superadmin')),

                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(table: User::class, column: 'email', ignorable: fn($record) => $record)
                    ->disabled(fn($record) => auth()->user()->hasRole('superadmin') && $record->hasRole('superadmin')),

                TextInput::make('password')
                    ->password()
                    ->label('Password')
                    ->required(fn(string $context) => $context === 'create')
                    ->requiredWith('passwordConfirmation')
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUsers || $livewire instanceof Pages\EditUsers)
                    ->disabled(fn($record) => auth()->user()->hasRole('superadmin') && $record->hasRole('superadmin')),

                TextInput::make('passwordConfirmation')
                    ->password()
                    ->label('Confirm Password')
                    ->required(fn(string $context) => $context === 'create')
                    ->requiredWith('password')
                    ->minLength(8)
                    ->same('password')
                    ->dehydrated(false)
                    ->visible(fn($livewire) => $livewire instanceof Pages\CreateUsers || $livewire instanceof Pages\EditUsers)
                    ->disabled(fn($record) => auth()->user()->hasRole('superadmin') && $record->hasRole('superadmin')),

                    Select::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->disabled(fn($record) => auth()->user()->hasRole('superadmin') && $record->hasRole('superadmin'))
                    ->options(function() {
                        return Role::where('name', '!=', 'superadmin')->pluck('name', 'id'); 
                    }),                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->can('user.edit') && !$record->hasRole('superadmin')),
                Tables\Actions\DeleteAction::make('Delete')
                    ->visible(fn($record) => auth()->user()->can('user.delete') && !$record->hasRole('superadmin')),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUsers::route('/create'),
            'edit' => Pages\EditUsers::route('/{record}/edit'),
        ];
    }
}