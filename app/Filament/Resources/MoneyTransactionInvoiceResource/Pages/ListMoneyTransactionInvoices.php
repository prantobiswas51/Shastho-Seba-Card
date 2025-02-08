<?php

namespace App\Filament\Resources\MoneyTransactionInvoiceResource\Pages;

use App\Filament\Resources\MoneyTransactionInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMoneyTransactionInvoices extends ListRecords
{
    protected static string $resource = MoneyTransactionInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
