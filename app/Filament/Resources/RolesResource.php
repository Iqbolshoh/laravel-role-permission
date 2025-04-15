<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RolesResource\Pages;
use Filament\Forms\Components\Section;
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
use Illuminate\Database\Eloquent\Builder;

class RolesResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static string $view = 'filament.pages.roles';
    protected static ?string $navigationGroup = 'Roles & Users';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label('Role Name'),

                ...Permission::all()
                    ->pluck('name')
                    ->groupBy(fn($perm) => explode('.', $perm, 2)[0])
                    ->map(function ($permissions, $group) {
                        return Section::make(ucfirst($group))
                            ->schema([
                                Forms\Components\CheckboxList::make('permissions')
                                    ->options(
                                        collect($permissions)->mapWithKeys(function ($perm) {
                                            return [$perm => ucfirst(explode('.', $perm)[1])];
                                        })
                                    )
                                    ->label('Permissions')
                                    ->columns(min(4, count($permissions)))
                                    ->bulkToggleable()
                            ])
                            ->collapsible()
                            ->compact();
                    })
                    ->all(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),
                TextColumn::make('name')->label('Role Name'),
                TextColumn::make('permissions.name')
                    ->label('Permissions')
                    ->limit(2)
                    ->badge()
                    ->separator(', ')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
