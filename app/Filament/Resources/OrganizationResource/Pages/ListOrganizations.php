<?php

namespace App\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\OrganizationResource;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Organization')->icon('heroicon-o-plus')->color('success')
            ->visible(fn ($record) => Auth::user()->role === 'SuperAdmin'),
        ];
    }
}

