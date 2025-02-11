<?php

namespace App\Filament\Resources\CardResource\Pages;

use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use App\Filament\Resources\CardResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCard extends CreateRecord
{
    protected static string $resource = CardResource::class;

    protected function getFormActions(): array
    {
        return [];
    }
}
