<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolesResource\Pages;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class RolesResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.roles';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 2;

    /**
     * Access Control: Determines if the user can access this page.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superadmin');
    }

    /**
     * Form Configuration: Defines fields for role creation and editing.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Role Name')
                    ->rule('regex:/^[a-zA-Z0-9-]+$/')
                    ->helperText('Only letters (a-z, A-Z), numbers (0-9), and dashes (-) are allowed.')
                    ->disabled(fn($record) => $record && $record->name === 'superadmin'),

                Select::make('permissions')
                    ->relationship('permissions', 'name')
                    ->label('Permissions')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->required(fn($record) => $record && $record->name !== 'superadmin')
                    ->minItems(fn($record) => $record && $record->name !== 'superadmin' ? 1 : null)
                    ->disabled(fn($record) => $record && $record->name === 'superadmin')
                    ->options(function () {
                        return Permission::all()
                            ->groupBy(function ($permission) {
                                return explode('.', $permission->name)[0];
                            })
                            ->mapWithKeys(function ($group, $key) {
                                return [
                                    ucfirst($key) => $group->pluck('name', 'id'),
                                ];
                            });
                    }),
            ]);
    }

    /**
     * Table Configuration: Configures the table for displaying roles.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('ID'),

                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->label('Role Name'),

                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->badge()
                    ->searchable()
                    ->separator(', '),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                SelectFilter::make('permissions')
                    ->label('Filter by Permission')
                    ->options(function () {
                        return Permission::pluck('name', 'id')->toArray();
                    })
                    ->multiple()
                    ->preload()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->whereHas('permissions', function (Builder $subQuery) use ($data) {
                                $subQuery->whereIn('permissions.id', $data['values']);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => $record->name !== 'superadmin'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->name !== 'superadmin'),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'asc');
    }

    /**
     * Relations Configuration: No related models defined.
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Page Routes Configuration: Defines routes for role management.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRoles::route('/create'),
            'edit' => Pages\EditRoles::route('/{record}/edit'),
        ];
    }
}