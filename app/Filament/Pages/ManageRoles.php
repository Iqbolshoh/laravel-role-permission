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
    public function canEdit(Role $record): bool
    {
        return auth()->check() && auth()->user()->can('role.edit');
    }

    // Check if user can delete a role (role.delete)
    public function canDelete(Role $record): bool
    {
        return auth()->check() && auth()->user()->can('role.delete');
    }

    protected function getTableQuery()
    {
        return Role::query()->with('permissions'); // Preload permissions
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('name')->label('Role Name')->searchable(),
            TextColumn::make('permissions')
                ->label('Permissions')
                ->getStateUsing(fn(Role $record) => $record->permissions->pluck('name')->implode(', '))
                ->wrap()
                ->searchable(),
            TextColumn::make('created_at')->label('Created At')->dateTime(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn(Role $record) => $this->canEdit($record))
                ->form(fn(Form $form, $record) => $this->getEditForm($form, $record))
                ->fillForm(function (Role $record): array {
                    return [
                        'roleName' => $record->name,
                        'permissions' => $record->permissions->pluck('name')->toArray(),
                    ];
                })
                ->action(function (Role $record, array $data): void {
                    try {
                        $record->update(['name' => $data['roleName']]);
                        $record->syncPermissions($data['permissions'] ?? []);
                        Utils::notify('Success', "Role '{$record->name}' updated!", 'success');
                    } catch (\Exception $e) {
                        Utils::notify('Error', 'Update failed: ' . $e->getMessage(), 'danger');
                    }
                })
                ->requiresConfirmation(),

            DeleteAction::make()
                ->visible(fn(Role $record) => $this->canDelete($record)),
        ];
    }

    protected function getTableBulkActions(): array
    {
        $bulkActions = [];

        // Only show bulk delete if user has role.delete permission
        if ($this->canDelete(new Role())) { // Using a new Role instance as a placeholder
            $bulkActions[] = BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->visible(fn() => $this->canDelete(new Role())),
            ]);
        }

        return $bulkActions;
    }

    // Edit form schema
    protected function getEditForm(Form $form, Role $record): Form
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
                ->unique(Role::class, 'name', $record)
                ->validationMessages([
                    'unique' => 'This role name already exists.',
                    'regex' => 'Only letters (A-Z) (a-z), numbers (0-9), and underscores (_) allowed.'
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