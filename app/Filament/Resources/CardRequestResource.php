<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Card;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CardRequest;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CardRequestResource\Pages;
use App\Filament\Resources\CardRequestResource\RelationManagers;

class CardRequestResource extends Resource
{


    protected static ?string $model = CardRequest::class;
    protected static ?string $navigationGroup = 'Cards';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canCreate(): bool
    {
        return Auth::user()->role === 'admin'; // Only admins can create requests
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->role === 'SuperAdmin'; // Only super admins can approve/reject
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('number')
                    ->required()
                    ->label('Card Number')
                    ->unique('cards', 'number') // Ensure the number is unique in the `cards` table
                    ->rules([
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('cards', 'number'), // Alternative way to define unique rule
                    ]),
                Select::make('type')
                    ->options([
                        'gold' => 'Gold',
                        'silver' => 'Silver',
                        'platinum' => 'Platinum',
                    ])
                    ->required()
                    ->label('Card Type'),
                Hidden::make('admin_id')->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label('Card Number'),
                Tables\Columns\TextColumn::make('type')->label('Card Type'),
                BadgeColumn::make('request_status')->colors([
                    'warning' => 'pending',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ]),
                Tables\Columns\TextColumn::make('admin.name')->label('Requested By'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->modalHeading('Approve Card Request')
                    ->modalSubheading('Please confirm the card details and set the price.')
                    ->form([
                        TextInput::make('price')
                            ->label('Card Price')
                            ->required()
                            ->numeric()
                            ->minValue(0), // Ensure the price is a positive number
                    ])
                    ->icon('heroicon-o-hand-thumb-up')
                    ->color('success')->action(function (CardRequest $record, array $data) {
                        $record->update(['request_status' => 'approved']);

                        Card::create([
                            'user_id' => $record->admin_id,
                            'number' => $record->number,
                            'type' => $record->type,
                            'price' => $data['price'],
                            'status' => 'Inactive',
                        ]);
                    })
                    ->visible(fn ($record) => Auth::user()->role === 'SuperAdmin' && $record->request_status === 'pending')
                    ->modalButton('Approve'),

                Action::make('reject')->icon('heroicon-o-hand-thumb-down')->color('danger')
                ->label('Reject')
                ->visible(fn ($record) => Auth::user()->role === 'SuperAdmin' && $record->request_status === 'pending')
                ->action(function ($record) {
                    $record->update(['request_status' => 'rejected']);

                    \Filament\Notifications\Notification::make()
                        ->title('Card Request Rejected')
                        ->body('The card has been rejected.')
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Are you sure you want to reject?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCardRequests::route('/'),
            'create' => Pages\CreateCardRequest::route('/create'),
            'edit' => Pages\EditCardRequest::route('/{record}/edit'),
        ];
    }
}
