<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;
use Filament\Actions\Action;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 1;

    public array $formData = [
        'roleName' => '',
        'permissions' => [],
    ];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.create');
    }

    public function form(Form $form): Form
    {
        $permissions = Permission::all()->pluck('name')->groupBy(function ($perm) {
            return explode('.', $perm, 2)[0];
        })->map(function ($group) {
            return $group->mapWithKeys(function ($perm) {
                return [$perm => ucfirst(explode('.', $perm)[1])];
            })->all();
        })->all();

        $schema = [
            Section::make('Role Details')
                ->description('Enter the role name and assign permissions below.')
                ->schema([
                    TextInput::make('roleName')
                        ->label('Role Name')
                        ->required()
                        ->regex('/^[a-zA-Z0-9_]+$/')
                        ->maxLength(255)
                        ->placeholder('e.g., admin_role')
                        ->unique(table: Role::class, column: 'name')
                        ->validationMessages([
                            'unique' => 'This role name already exists.',
                            'regex' => 'Role name can only contain letters, numbers, and underscores.',
                        ]),
                ])
                ->collapsible()
                ->compact(),
        ];

        foreach ($permissions as $group => $perms) {
            $schema[] = Section::make(ucfirst($group) . ' Permissions')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->options($perms)
                        ->columns(min(4, count($perms)))
                        ->bulkToggleable()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) use ($group, $permissions) {
                            $groupPerms = array_keys($permissions[$group]);
                            $selectedGroupPerms = array_intersect($state ?? [], $groupPerms);
                            $set(
                                "select_all_{$group}",
                                count($selectedGroupPerms) === count($groupPerms)
                            );
                        }),
                ])
                ->collapsible()
                ->compact();
        }

        return $form
            ->schema($schema)
            ->statePath('formData');
    }

    public function save()
    {
        $validated = $this->form->getState();
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $validated['roleName'])) {
            Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
            return;
        }

        if (Role::where('name', $validated['roleName'])->exists()) {
            Utils::notify(
                'Role Already Exists',
                "Role '{$validated['roleName']}' already exists! Please choose another name.",
                'danger'
            );
            return;
        }

        if (empty($validated['permissions'])) {
            Utils::notify(
                'No Permissions Selected',
                'You must select at least one permission to create a role.',
                'warning'
            );
            return;
        }

        try {
            $role = Role::create(['name' => $validated['roleName']]);
            $role->syncPermissions($validated['permissions']);

            Utils::notify('Success', "Role '{$role->name}' created successfully!", 'success');
            $this->reset('formData');
            $this->dispatch('roleCreated');
        } catch (\Exception $e) {
            Utils::notify('Error', 'Failed to create role: ' . $e->getMessage(), 'danger');
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Create Role')
                ->submit('save')
                ->color('primary'),
        ];
    }
}