<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BalanceRequest;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BalanceRequestResource\Pages;
use App\Filament\Resources\BalanceRequestResource\RelationManagers;

class BalanceRequestResource extends Resource
{
    protected static ?string $model = BalanceRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Balance Request';

    protected static ?string $navigationGroup = 'Funds Administration';
    
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'admin'; // Only Admins can create
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only Superadmin can edit
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only Superadmin can delete
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Admin Withdrawal Request.')
                            ->description('Instead of requesting a balance, admins can request a withdrawal from the available pool.')
                            ->schema([
                                Select::make('approved_by')
                                    ->label('Select Superadmin')
                                    ->relationship('approvedBy', 'name', function ($query) {
                                        return $query->where('role', 'SUPERADMIN');
                                    })
                                    ->required(),
                                TextInput::make('amount')
                                    ->label('Request Amount')
                                    ->numeric()
                                    ->required(),
                                Hidden::make('admin_id')->default(auth()->id()),
                            ])
                    ])->columnSpanFull()
            ]);
    }




    public static function table(Table $table): Table
{
    return $table
        ->query(BalanceRequest::query()->when(
            Auth::user()->role === 'admin',
            fn ($q) => $q->where('admin_id', Auth::id())
        ))
        ->columns([
            TextColumn::make('id')
                ->label('ID')
                ->numeric()
                ->searchable()
                ->toggleable(true)
                ->sortable(),
            TextColumn::make('admin.name')->label('Requested By')
                ->sortable()
                ->searchable(),
            TextColumn::make('amount')->label('Amount')->money('BDT')
                ->sortable()
                ->searchable(),
            BadgeColumn::make('status')->colors([
                'warning' => 'pending',
                'success' => 'approved',
                'danger' => 'rejected',
            ]),
            TextColumn::make('approvedBy.name')->label('Approved By')
                ->sortable()
                ->searchable()
                ->default('-'),
            TextColumn::make('created_at')->label('Requested At')
                ->dateTime()
                ->sortable()
                ->searchable(),
        ])
        ->actions([
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-hand-thumb-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Are you sure you want to approve?')
                ->visible(fn ($record) => Auth::user()->role === 'SUPERADMIN' && $record->status === 'pending')
                ->action(function ($record) {
                    $superadmin = Auth::user(); // Get the logged-in superadmin
                    $admin = $record->admin;   // Get the admin who requested the balance

                    // Ensure Superadmin has enough balance before approving
                    if ($superadmin->balance < $record->amount) {
                        \Filament\Notifications\Notification::make()
                            ->title('Insufficient Balance')
                            ->body('You do not have enough balance to approve this request.')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Transaction: Deduct from Superadmin and Add to Admin
                    DB::transaction(function () use ($superadmin, $admin, $record) {
                        $superadmin->decrement('balance', $record->amount);
                        $admin->increment('balance', $record->amount);

                        // Update the balance request status
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => $superadmin->id,
                        ]);
                    });

                    \Filament\Notifications\Notification::make()
                        ->title('Balance Request Approved')
                        ->body('You have successfully approved the balance request.')
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-hand-thumb-down')
                ->visible(fn ($record) => Auth::user()->role === 'SUPERADMIN' && $record->status === 'pending')
                ->action(function ($record) {
                    $record->update(['status' => 'rejected']);

                    \Filament\Notifications\Notification::make()
                        ->title('Balance Request Rejected')
                        ->body('The balance request has been rejected.')
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Are you sure you want to reject?')
                ->color('danger'),
            Tables\Actions\EditAction::make()
        ]);
}







    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBalanceRequests::route('/'),
            'create' => Pages\CreateBalanceRequest::route('/create'),
        ];
    }
}
