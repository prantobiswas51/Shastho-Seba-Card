<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\LoadMoney;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LoadMoneyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\LoadMoneyResource\RelationManagers;

use Filament\Tables\Columns\Summarizers\Sum;

class LoadMoneyResource extends Resource
{
    protected static ?string $model = LoadMoney::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Load Money';
    protected static ?string $modelLabel = 'Add-money';
    protected static ?string $navigationGroup = 'Fund Management';
    protected static ?string $slug = 'load-money';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can view transactions
    }

    public static function canViews(): bool
    {
        return Auth::user()->role === 'admin'; // Only SUPERADMIN can create
    }
    public static function canCreate(): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can create
    }

    // load money can edit
    public static function canEdit($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can edit
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->role === 'SUPERADMIN'; // Only SUPERADMIN can edit
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Hidden::make('superadmin_id')->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->numeric()
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('superadmin.name')->label('SuperAdmin Name')->toggleable(),

                TextColumn::make('amount')
                    ->summarize(Sum::make()
                        ->label('Total Load Money : ')
                        ->money('BDT')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListLoadMoney::route('/'),
            'create' => Pages\CreateLoadMoney::route('/create'),
            'view' => Pages\ViewLoadMoney::route('/{record}'),
            'edit' => Pages\EditLoadMoney::route('/{record}/edit'),
        ];
    }
}
