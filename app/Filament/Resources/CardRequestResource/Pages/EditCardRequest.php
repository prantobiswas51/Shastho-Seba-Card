<?php

namespace App\Filament\Resources\CardRequestResource\Pages;

use App\Filament\Resources\CardRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCardRequest extends EditRecord
{
    protected static string $resource = CardRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
