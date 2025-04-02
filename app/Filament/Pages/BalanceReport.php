<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Http\Request;

class BalanceReport extends Page
{
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Card and Balance Report';
    protected static string $view = 'filament.pages.balance-report';

    public $users;
    public $search = ''; // Add a search property

    public function mount(Request $request)
    {
        // Get the search query from the request
        $this->search = $request->query('search', '');

        // Query users with card counts and filter by search
        $this->users = User::withCount([
                'cards as silver_cards' => function ($query) {
                    $query->where('type', 'Silver')->where('status', 'Inactive');
                },
                'cards as gold_cards' => function ($query) {
                    $query->where('type', 'Gold')->where('status', 'Inactive');
                },
                'cards as platinum_cards' => function ($query) {
                    $query->where('type', 'Platinum')->where('status', 'Inactive');
                },
            ])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('id', 'like', '%' . $this->search . '%');
            })->get();
    }


}
