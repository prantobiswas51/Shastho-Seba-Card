<?php

namespace App\Filament\Resources\FundTransferResource\Pages;

use App\Filament\Resources\FundTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFundTransfers extends ListRecords
{
    protected static string $resource = FundTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Send Money to Admins')->color('success')->icon('heroicon-o-paper-airplane'),
        ];
    }
}
