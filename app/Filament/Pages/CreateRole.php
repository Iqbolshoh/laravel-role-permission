<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
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
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static string $view = 'filament.pages.create-role';
    protected static ?string $navigationGroup = 'Roles';
    protected static ?int $navigationSort = 2;

    public array $formData = [
        'roleName' => '',
        'permissions' => [],
    ];

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    | Determines if the authenticated user has permission to access this page.
    */
    public static function canAccess(): bool
    {
        return auth()->user()?->can('role.create') ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Form Definition
    |--------------------------------------------------------------------------
    | Define the form structure for creating a role.
    */
    public function form(Form $form): Form
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
                ->unique(Role::class, 'name')
                ->validationMessages([
                    'unique' => 'This role name already exists.',
                    'regex' => 'Only letters (A-Z) (a-z), numbers (0-9), and underscores (_) allowed.'
                ])
        ];

        foreach ($permissions as $group => $perms) {
            $schema[] = Section::make(ucfirst($group))
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('')
                        ->options($perms)
                        ->columns(min(4, count($perms)))
                        ->bulkToggleable()
                        ->reactive()
                        ->afterStateUpdated(fn($state, callable $set) => $set(
                            "select_all_{$group}",
                            count(array_intersect($state ?? [], array_keys($perms))) === count($perms)
                        )),
                ])
                ->collapsible()
                ->compact();
        }

        return $form->schema($schema)->statePath('formData');
    }

    /*
    |--------------------------------------------------------------------------
    | Role Creation
    |--------------------------------------------------------------------------
    | Handle role creation with validation and permission assignment.
    */
    public function create(): void
    {
        $validated = $this->form->getState();

        if (empty($validated['permissions'])) {
            Utils::notify('No Permissions', 'Select at least one permission.', 'warning');
            return;
        }

        try {
            $role = Role::create(['name' => $validated['roleName']]);
            $role->syncPermissions($validated['permissions']);

            Utils::notify('Success', "Role '{$role->name}' created!", 'success');
            $this->form->fill();
            $this->dispatch('roleCreated');
        } catch (\Exception $e) {
            Utils::notify('Error', 'Creation failed: ' . $e->getMessage(), 'danger');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Form Actions
    |--------------------------------------------------------------------------
    | Define the form submission actions.
    */
    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Create Role')
                ->action('create')
                ->color('primary'),
        ];
    }
}
