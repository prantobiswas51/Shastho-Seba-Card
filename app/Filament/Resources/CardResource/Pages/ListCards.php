<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Models\Card;
use App\Models\User;
use Filament\Actions;
use App\Models\CardTransaction;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Filament\Resources\CardResource;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Assign New Card')->icon('heroicon-o-paper-airplane'),
            Actions\Action::make('generateCards')
                ->label('Assign Bulk Cards')
                ->color('success')
                ->icon('heroicon-o-paper-airplane')
                ->form([
                    Select::make('user_id')
                        ->label('Select Admin')
                        ->options(User::where('role', 'admin')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('start_number')
                        ->label('Start Number')
                        ->numeric()
                        ->required(),
                    TextInput::make('end_number')
                        ->label('End Number')
                        ->numeric()
                        ->required(),
                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'gold' => 'Gold',
                            'silver' => 'Silver',
                            'platinum' => 'Platinum',
                        ])
                        ->required(),
                    TextInput::make('price')
                        ->label('Price')
                        ->numeric()
                        ->required()
                        ->minValue(0),
                ])
                ->action(function (array $data) {
                    // Validate and generate cards
                    $startNumber = $data['start_number'];
                    $endNumber = $data['end_number'];
                    $type = $data['type'];
                    $price = $data['price'];
                    $userId = $data['user_id']; // Selected admin user ID

                    if ($startNumber > $endNumber) {
                        Notification::make()
                            ->title('Error')
                            ->danger()
                            ->body('Start number must be less than or equal to end number.')
                            ->send();
                        return;
                    }

                    // Initialize card counts
                    $cardCounts = [
                        'silver' => 0,
                        'gold' => 0,
                        'platinum' => 0,
                    ];

                    // Fetch all existing card numbers in the range for bulk validation
                    $existingNumbers = Card::whereBetween('number', [$startNumber, $endNumber])
                        ->pluck('number')
                        ->toArray();

                    // Loop through the range and create cards
                    for ($number = $startNumber; $number <= $endNumber; $number++) {
                        if (in_array($number, $existingNumbers)) {
                            Notification::make()
                                ->title('Error')
                                ->danger()
                                ->body("Card number $number already exists.")
                                ->send();
                            continue;
                        }

                        // Create the card and assign it to the selected admin
                        Card::create([
                            'number' => $number,
                            'type' => $type,
                            'price' => $price,
                            'status' => 'Inactive', // Default status
                            'user_id' => $userId, // Assign to the selected admin
                        ]);

                        // Increment the card count for the type
                        $cardCounts[$type]++;
                    }

                    // Format the card counts as a string
                    $formattedCards = sprintf(
                        "Silver = %d, Gold = %d, Platinum = %d, Total = %d",
                        $cardCounts['silver'],
                        $cardCounts['gold'],
                        $cardCounts['platinum'],
                        array_sum($cardCounts)
                    );

                    // Save the transaction
                    CardTransaction::create([
                        'superadmin_id' => auth()->id(),
                        'admin_id' => $userId,
                        'cards' => $formattedCards,
                    ]);

                    Notification::make()
                        ->title('Success')
                        ->success()
                        ->body('Cards generated and transaction saved successfully.')
                        ->send();
                })
                ->visible(fn ($record) => Auth::user()->role === 'SuperAdmin'),
        ];
    }
}
