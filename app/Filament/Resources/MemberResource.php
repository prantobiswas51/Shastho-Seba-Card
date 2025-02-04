<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Member;
use App\Models\District;
use App\Models\SubDistrict;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MemberResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MemberResource\RelationManagers;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('fatherName')->required(),
                TextInput::make('nid')->required(),
                TextInput::make('dob')->required(),
                TextInput::make('address')->required(),
                Select::make('card_id')
                    ->label('Assign Card')
                    ->options(fn() => auth()->user()->cards()->pluck('number', 'id'))
                    ->nullable(),
                Hidden::make('admin_id')->default(auth()->id()),
                 // Select District
                 Select::make('district_id')
                 ->label('District')
                 ->options(District::all()->sortBy('name')->pluck('name', 'id'))
                 ->reactive()->required()
                 ->afterStateUpdated(fn (callable $set) => $set('sub_district_id', null)),
                 Select::make('sub_district_id')
                 ->label('Sub District')->required()
                 ->options(function (callable $get) {
                 $districtId = $get('district_id');
                     if (!$districtId) {
                         return SubDistrict::all()->pluck('name', 'id');
                     }
                     return SubDistrict::where('district_id', $districtId)->pluck('name', 'id');
                 })->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('nid'),
                TextColumn::make('name'),
                TextColumn::make('card.number')->label('Assigned Card'),
                TextColumn::make('card.type')->label('Type'),
                TextColumn::make('card.price')->label('Price'),
                TextColumn::make('district.name')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
