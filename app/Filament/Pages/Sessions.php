<?php

namespace App\Filament\Pages;

use App\Models\Session;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Sessions extends Page implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-device-tablet';
    protected static ?string $navigationLabel = 'Active Sessions';
    protected static ?string $navigationGroup = 'Account';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.sessions';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('profile.view');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user_agent')
                    ->label('Device Name')
                    ->sortable()
                    ->limit(50),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->sortable(),

                TextColumn::make('last_activity')
                    ->label('Last Activity')
                    ->dateTime()
                    ->sortable(),
            ])
            ->query(Session::query()->where('user_id', Auth::id()))
            ->defaultSort('last_activity', 'desc')
            ->actions([
                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Session $record) {
                        if ($record->id === session()->getId()) {
                            Auth::logout();
                            session()->invalidate();
                            session()->regenerateToken();
                            $this->redirect('/login');
                        }
                        DB::table('sessions')->where('id', $record->id)->delete();
                        \Log::info("Session ID [{$record->id}] deleted.");
                    }),
            ]);
    }
}
