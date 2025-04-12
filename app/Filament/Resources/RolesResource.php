<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolesResource\Pages;
use App\Models\Role;
use App\Models\Permission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;

class RolesResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        // Get all permissions and group them by their "category"
        $permissions = Permission::all()
            ->pluck('name')
            ->groupBy(fn($perm) => explode('.', $perm, 2)[0]) // Group by first part before dot
            ->map(fn($group) => $group->mapWithKeys(fn($perm) => [$perm => ucfirst(explode('.', $perm)[1])])->all())
            ->all();

        // Form schema
        return $form
            ->schema([
                TextInput::make('name')
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

                // Dynamically adding permissions based on categories
                ...array_map(
                    fn($group, $permissions) => Section::make(ucfirst($group))
                        ->schema([
                            CheckboxList::make('permissions')
                                ->options($permissions)
                                ->columns(min(4, count($permissions))) // Adjust column number dynamically
                                ->bulkToggleable()
                        ])
                        ->collapsible()
                        ->compact(),
                    array_keys($permissions),
                    $permissions
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Role Name')
                    ->sortable(),
            ])
            ->filters([
                // Define filters here if needed
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRoles::route('/create'),
            'edit' => Pages\EditRoles::route('/{record}/edit'),
        ];
    }
}
