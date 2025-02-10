<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }


    public function boot(): void
    {
        // Filament::registerStyles([
        //     asset('css/filament-custom.css'),
        // ]);

        // Filament::serving(function () {
        //     Filament::registerUserMenuItems([
        //         UserMenuItem::make()
        //             ->label(auth()->user()->role) // Display the user's role
        //             ->icon('heroicon-s-user'), // Optional: Add an icon
        //     ]);
        // });
    }
}
