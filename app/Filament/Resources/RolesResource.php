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

            Select::make('permissions')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->label('Permissions')
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
