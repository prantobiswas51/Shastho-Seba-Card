<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Models\MoneyTransactionInvoice;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MoneyTransactionInvoiceResource\Pages;
use App\Filament\Resources\MoneyTransactionInvoiceResource\RelationManagers;

class MoneyTransactionInvoiceResource extends Resource
{
    protected static ?string $model = MoneyTransactionInvoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static ?string $navigationLabel = 'Transaction Details';
    protected static ?string $modelLabel = 'Transaction Lists';
    protected static ?string $navigationGroup = 'Fund Management';
    protected static ?int $navigationSort = 3;



    public static function canViews(): bool
    {
        return Auth::user()->role === 'admin'; // Only SUPERADMIN can create
    }
    public static function canCreate(): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can create
    }


    public static function canEdit($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can edit
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can edit
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('superadmin.name')
                    ->label('Transfer By')
                    ->sortable(),
                TextColumn::make('admin.name')
                    ->label('Transfer To')
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('transaction_id')
                    ->searchable(),
                // TextColumn::make('amount')
                //     ->summarize(Sum::make()
                //     ->label('Total Transaction Received Money : ')
                //     ->money('BDT')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMoneyTransactionInvoices::route('/'),
            'create' => Pages\CreateMoneyTransactionInvoice::route('/create'),
            'edit' => Pages\EditMoneyTransactionInvoice::route('/{record}/edit'),
        ];
    }
}
