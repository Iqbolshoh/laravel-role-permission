<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolesResource\Pages;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 2;

    /**
     * Restrict access to superadmins only.
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('superadmin') ?? false;
    }

    /**
     * Form configuration for role creation and editing.
     */
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->label('Role Name')
                ->regex('/^[a-zA-Z0-9-]+$/')
                ->helperText('Only letters, numbers, and dashes are allowed.')
                ->disabled(fn($record) => $record?->name === 'superadmin'),

            Select::make('permissions')
                ->relationship('permissions', 'name')
                ->label('Permissions')
                ->multiple()
                ->searchable()
                ->preload()
                ->required(fn($record) => $record?->name !== 'superadmin')
                ->minItems(fn($record) => $record?->name !== 'superadmin' ? 1 : 0)
                ->hidden(fn($record) => $record?->name === 'superadmin')
                ->options(static::getGroupedPermissions()),
        ]);
    }

    /**
     * Cache and group permissions for the form.
     */
    protected static function getGroupedPermissions(): array
    {
        return Cache::remember('grouped_permissions', now()->addHours(24), function () {
            return Permission::all()
                ->groupBy(fn($perm) => explode('.', $perm->name)[0])
                ->mapWithKeys(fn($group, $key) => [
                    ucfirst($key) => $group->pluck('name', 'id')->toArray(),
                ])->toArray();
        });
    }

    /**
     * Sync permissions for a role.
     */
    public static function syncPermissions(Role $role, array $permissionIds): void
    {
        if (!empty($permissionIds)) {
            $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }
    }

    /**
     * Table configuration for displaying roles.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable()->label('ID'),
                TextColumn::make('name')->sortable()->searchable()->label('Role Name'),
                TextColumn::make('permissions.name')->label('Permissions')->searchable()->badge(),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Created At')->toggleable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->label('Updated At')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('permissions')
                    ->label('Permissions')
                    ->options(fn() => Permission::pluck('name', 'id')->toArray())
                    ->multiple()
                    ->preload()
                    ->query(fn(Builder $query, array $data) => $data['values'] ? $query->whereHas('permissions', fn(Builder $subQuery) => $subQuery->whereIn('id', $data['values'])) : $query),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn($record) => $record->name !== 'superadmin'),
                Tables\Actions\DeleteAction::make()->visible(fn($record) => $record->name !== 'superadmin'),
            ])
            ->bulkActions([])
            ->defaultSort('id', 'asc');
    }

    /**
     * Relations configuration.
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Page routes configuration.
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