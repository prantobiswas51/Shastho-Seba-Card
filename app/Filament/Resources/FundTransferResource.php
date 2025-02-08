<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\LoadMoney;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\FundTransfer;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use App\Models\MoneyTransactionInvoice;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\FundTransferResource\Pages;
use Filament\Tables\Columns\Summarizers\Sum;
class FundTransferResource extends Resource
{
    protected static ?string $model = FundTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Fund Transfer';
    protected static ?string $navigationGroup = 'Fund Management';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Auth::user() && Auth::user()->role === 'SUPERADMIN';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Information')
                    ->schema([
                        Select::make('user_id')
                            ->label('Select User')
                            ->relationship('user', 'name')
                            ->required(),
                    ]),

                Section::make('Fund Transfer')
                    ->schema([
                        TextInput::make('amount')
                            ->label('Enter Amount')
                            ->numeric()
                            ->required(),
                    ]),

                Actions::make([
                    Action::make('transferMoney')
                        ->label('Transfer Money')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($livewire) {
                            $data = $livewire->form->getState();
                            static::transferMoney($data);
                        }),
                ])->columnSpanFull(),
            ]);
    }

    public static function transferMoney($data)
    {
        DB::beginTransaction();

        try {
            if (empty($data['user_id']) || empty($data['amount'])) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('User or amount is missing.')
                    ->send();
                return;
            }

            $user = User::find($data['user_id']);
            if (!$user || $user->role !== 'admin') {
                throw new \Exception('Invalid user selection.');
            }

            $availableBalance = LoadMoney::sum('amount');
            if ($availableBalance < $data['amount']) {
                throw new \Exception('Insufficient funds available for this transfer.');
            }

            $loadMoney = LoadMoney::first();
            if (!$loadMoney) {
                throw new \Exception('No funds available.');
            }

            $loadMoney->decrement('amount', $data['amount']);
            $userTransaction = MoneyTransactionInvoice::firstOrNew(['admin_id' => $data['user_id']]);
            $userTransaction->increment('amount', $data['amount']);
            $userTransaction->decrement('amount', $data['amount']);
            $userTransaction->save();
            MoneyTransactionInvoice::create([
                'superadmin_id' => Auth::id(),
                'admin_id' => $data['user_id'],
                'amount' => $data['amount'],
                'note' => 'Money Transfer',
            ]);

            DB::commit();

            Notification::make()
                ->title('Money Sent Successfully')
                ->success()
                ->send();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Transaction Failed')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('superadmin.name')
                    ->label('Transfer By')
                    ->sortable(),

                TextColumn::make('admin.name')
                    ->label('Transfer To')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('amount')
                    ->summarize(Sum::make()
                    ->label('Total Fund Transfer Money : ')
                    ->money('BDT')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFundTransfers::route('/'),
            'create' => Pages\CreateFundTransfer::route('/create'),
            'edit' => Pages\EditFundTransfer::route('/{record}/edit'),
        ];
    }
}
