<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\Utils;
use Filament\Actions\Action;

class CreateRole extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
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
        $permissions = Permission::all()->pluck('name', 'name')->toArray();

        $groupedPermissions = [];
        foreach ($permissions as $permission) {
            [$group, $action] = explode('.', $permission, 2);
            $groupedPermissions[$group][] = $permission;
        }

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
                        ->extraAttributes(['class' => 'w-full']),
                ])
                ->collapsible()
                ->compact(),
        ];

        foreach ($groupedPermissions as $group => $perms) {
            $schema[] = Section::make(ucfirst($group) . ' Permissions')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->options(array_combine($perms, array_map(fn($perm) => ucfirst(explode('.', $perm)[1]), $perms)))
                        ->columns(count($perms))
                        ->default([])
                        ->extraAttributes(['class' => 'gap-4']),
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

        $roleName = $validated['roleName'];
        $permissions = $validated['permissions'];

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $roleName)) {
            return Utils::notify(
                'Invalid Role Name',
                "Role name can only contain letters (a-z, A-Z), numbers (0-9), and underscores (_).",
                'danger'
            );
        }

        if (Role::where('name', $roleName)->exists()) {
            return Utils::notify(
                'Role Already Exists',
                "Role '{$roleName}' already exists! Please choose another name.",
                'danger'
            );
        }

        if (empty($permissions)) {
            return Utils::notify(
                'No Permissions Selected',
                'You must select at least one permission to create a role.',
                'warning'
            );
        }

        $role = Role::create(['name' => $roleName]);
        $role->syncPermissions($permissions);

        Utils::notify('Success', "Role '{$roleName}' created!", 'success');

        $this->reset('formData');
        $this->dispatch('roleCreated');
    }

    protected function getActions(): array
    {
        return [
            Action::make('submitAction')
                ->label('Create Role')
                ->submit('save')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'px-6 py-3 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition ease-in-out duration-200',
                ]),
        ];
    }
}