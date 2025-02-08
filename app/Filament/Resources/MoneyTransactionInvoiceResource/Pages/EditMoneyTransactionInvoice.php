<?php

namespace App\Filament\Resources\MoneyTransactionInvoiceResource\Pages;

use App\Filament\Resources\MoneyTransactionInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMoneyTransactionInvoice extends EditRecord
{
    protected static string $resource = MoneyTransactionInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
