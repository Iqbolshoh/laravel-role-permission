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

    // Page access control (role.view)
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('role.view');
    }

    // Check if user can edit a role (role.edit)
    public function canEdit(Role $role): bool
    {
        return auth()->check() && auth()->user()->can('role.edit');
    }

    // Check if user can delete a role (role.delete)
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

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn(Role $role) => $this->canEdit($role))
                ->form(function (Form $form) {
                    return $this->getEditForm($form);
                })
                ->fillForm(fn(Role $role): array => [
                    'roleName' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ])
                ->action(function (Role $role, array $data): void {
                    if (empty($data['permissions'])) {
                        Utils::notify('No Permissions', 'Select at least one permission.', 'warning');
                        // Formani yopmaslik uchun, bu joyda faqat xabarni ko'rsatamiz
                        return; // Bu yerda hech qanday boshqa amal qilmaymiz, formani yopilmaydi
                    }

                    try {
                        $role->update(['name' => $data['roleName']]);
                        $role->syncPermissions($data['permissions']);
                        Utils::notify('Success', "Role '{$role->name}' updated!", 'success');
                    } catch (\Exception $e) {
                        Utils::notify('Error', 'Update failed: ' . $e->getMessage(), 'danger');
                    }
                })
                ->requiresConfirmation(),

            DeleteAction::make()
                ->visible(fn(Role $role) => $this->canDelete($role)),
        ];
    }

    protected function getTableBulkActions(): array
    {
        $bulkActions = [];

        if ($this->canDelete(new Role())) {
            $bulkActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn() => $this->canDelete(new Role())),
            ]);
        }

        return $bulkActions;
    }

    // Edit form schema
    protected function getEditForm(Form $form): Form
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
                    'regex' => 'Only letters (A-Z) (a-z), numbers (0-9), and underscores (_) allowed.',
                ]),
        ];

        foreach ($permissions as $group => $perms) {
            $schema[] = Section::make(ucfirst($group))
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->options($perms)
                        ->columns(min(4, count($perms)))
                        ->bulkToggleable(),
                ])
                ->collapsible()
                ->compact();
        }

        return $form->schema($schema);
    }
}