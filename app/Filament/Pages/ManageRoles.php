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
use Filament\Pages\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;

class ManageRoles extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.manage-roles';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('role.view');
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->can('role.create');
    }

    public function canEdit(Role $role): bool
    {
        return auth()->check() && auth()->user()->can('role.edit');
    }

    public function canDelete(Role $role): bool
    {
        return auth()->check() && auth()->user()->can('role.delete');
    }

    protected function getTableQuery()
    {
        return Role::query()->with('permissions');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('name')->label('Role Name')->searchable(),
            TextColumn::make('permissions')
                ->label('Permissions')
                ->getStateUsing(fn(Role $role) => $role->permissions->pluck('name')->implode(', '))
                ->wrap()
                ->searchable(),
            TextColumn::make('created_at')->label('Created At')->dateTime(),
        ];
    }

    protected function getFormSchema(): array
    {
        $permissions = Permission::all()
            ->pluck('name')
            ->groupBy(fn($perm) => explode('.', $perm, 2)[0])
            ->map(fn($group) => $group->mapWithKeys(fn($perm) => [$perm => ucfirst(explode('.', $perm)[1])])->all())
            ->all();

        $schema = [
            TextInput::make('roleName')
                ->label('Role Name')
                ->required()
                ->regex('/^[a-zA-Z0-9_]+$/')
                ->maxLength(255)
                ->placeholder('e.g., user_role')
                ->unique(Role::class, 'name', ignoreRecord: true)
                ->validationMessages([
                    'unique' => 'This role name already exists.',
                    'regex' => 'Only letters (A-Z), numbers (0-9), and underscores (_) allowed.',
                ]),
        ];

        foreach ($permissions as $group => $perms) {
            $schema[] = Section::make(ucfirst($group))
                ->schema([
                    CheckboxList::make('permissions')->options($perms)->columns(min(4, count($perms)))->bulkToggleable()
                ])
                ->collapsible()
                ->compact();
        }

        return $schema;
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn(Role $role) => $this->canEdit($role))
                ->form(fn(Form $form) => $form->schema($this->getFormSchema()))
                ->fillForm(fn(Role $role): array => [
                    'roleName' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ])
                ->action(fn(Role $role, array $data) => $this->updateRole($role, $data))
                ->color('primary')
                ->modalHeading('Edit Role')
                ->modalSubmitActionLabel('Update Role'),

            DeleteAction::make()
                ->visible(fn(Role $role) => $this->canDelete($role)),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return $this->canDelete(new Role())
            ? [BulkActionGroup::make([DeleteBulkAction::make()])]
            : [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Role')
                ->icon('heroicon-o-shield-check')
                ->form($this->getFormSchema())
                ->action(fn(array $data) => $this->createRole($data))
                ->color('primary')
                ->visible(fn() => auth()->user()?->can('role.create')),
        ];
    }

    protected function createRole(array $data): void
    {
        if (empty($data['permissions'])) {
            Utils::notify('No Permissions', 'Select at least one permission.', 'warning');
            return;
        }

        try {
            $role = Role::create(['name' => $data['roleName']]);
            $role->syncPermissions($data['permissions']);
            Utils::notify('Success', "Role '{$role->name}' created!", 'success');
        } catch (\Exception $e) {
            Utils::notify('Error', 'Creation failed: ' . $e->getMessage(), 'danger');
        }
    }

    protected function updateRole(Role $role, array $data): void
    {
        if (empty($data['permissions'])) {
            Utils::notify('No Permissions', 'Select at least one permission.', 'warning');
            return;
        }

        try {
            $role->update(['name' => $data['roleName']]);
            $role->syncPermissions($data['permissions']);
            Utils::notify('Success', "Role '{$role->name}' updated!", 'success');
        } catch (\Exception $e) {
            Utils::notify('Error', 'Update failed: ' . $e->getMessage(), 'danger');
        }
    }
}