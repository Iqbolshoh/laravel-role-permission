<?php

namespace App\Filament\Resources\SessionsResource\Pages;

use App\Filament\Resources\SessionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessions extends ListRecords
{
    protected static string $resource = SessionsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
