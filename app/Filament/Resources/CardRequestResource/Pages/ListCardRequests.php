<?php

namespace App\Filament\Resources\CardRequestResource\Pages;

use App\Filament\Resources\CardRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCardRequests extends ListRecords
{
    protected static string $resource = CardRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label("Request Card")->icon('heroicon-o-arrow-down-on-square'),
        ];
    }
}
