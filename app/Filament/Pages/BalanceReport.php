<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;

class BalanceReport extends Page
{
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Card and Balance Report';
    protected static string $view = 'filament.pages.balance-report';

    public $users;

    public function mount()
    {
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
        ])->get();
    }
}
