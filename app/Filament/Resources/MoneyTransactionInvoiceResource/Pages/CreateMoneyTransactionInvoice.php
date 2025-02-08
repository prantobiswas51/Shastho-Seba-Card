<?php

namespace App\Filament\Resources\MoneyTransactionInvoiceResource\Pages;

use App\Filament\Resources\MoneyTransactionInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMoneyTransactionInvoice extends CreateRecord
{
    protected static string $resource = MoneyTransactionInvoiceResource::class;
    protected function getFormActions(): array
    {
        return [];
    }
}
