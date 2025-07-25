<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Card;
use Filament\Tables;
use App\Models\Member;
use App\Models\District;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SubDistrict;
use Filament\Resources\Resource;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MemberResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MemberResource\RelationManagers;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create a Member')->description('Fill the necessary fields to create a member...')->collapsible()->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('fatherName')->required(),
                    TextInput::make('motherName')->required(),
                    TextInput::make('address')->required(),

                    Select::make('card_id')
                        ->label('Assign Card')
                        ->options(fn() => \App\Models\Card::whereNotIn('id', Member::pluck('card_id'))->pluck('number', 'id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if ($state) {
                                $card = \App\Models\Card::find($state);
                                $adminBalance = \App\Models\FundTransfer::where('admin_id', auth()->id())->value('amount');
                                dd($adminBalance);

                                if ($card && $adminBalance !== null) {
                                    // Check if balance is less than card price
                                    if ($adminBalance < $card->price) {
                                        $set('card_id', null); // Reset the card_id field
                                        Notification::make()
                                            ->title('Low Balance')
                                            ->body('Low balance, please recharge.')
                                            ->danger()
                                            ->send();
                                    } else {
                                        // Proceed with card assignment
                                        $card->update(['status' => 'Active']);
                                        \App\Models\FundTransfer::where('admin_id', auth()->id())
                                            ->decrement('amount', $card->price);
                                    }
                                }
                            }
                        }),



                    Hidden::make('admin_id')->default(auth()->id()),

                    Select::make('district_id')
                        ->label('District')
                        ->searchable()
                        ->options(District::all()->sortBy('name')->pluck('name', 'id'))
                        ->reactive()->required()
                        ->afterStateUpdated(fn(callable $set) => $set('sub_district_id', null)),
                    Select::make('sub_district_id')
                        ->label('Sub District')->required()
                        ->options(function (callable $get) {
                            $districtId = $get('district_id');
                            if (!$districtId) {
                                return SubDistrict::all()->pluck('name', 'id');
                            }
                            return SubDistrict::where('district_id', $districtId)->pluck('name', 'id');
                        })->reactive(),

                    Section::make('Family Members')
                        ->collapsible()
                        ->schema([
                            Repeater::make('family_members')
                                ->label('Family Members')
                                ->schema([
                                    TextInput::make('name')->required(),
                                    Select::make('gender')->required()->options([
                                        'Male' => 'Male',
                                        'Female' => 'Female',
                                        'Others' => 'Others',
                                    ]),
                                    TextInput::make('age')->numeric()->required(),
                                    TextInput::make('nid')->label('NID Number')->nullable(),
                                ])
                                ->defaultItems(1) // Minimum 1 family member
                                ->minItems(1)
                                ->maxItems(5)
                                ->label('Add Family Member'),
                        ])->columnSpan(1),
                ])->columnSpan(2),

                Section::make('Meta Info')->description('Fill the form')->collapsible()->schema([
                    FileUpload::make('member_photo')->required()->image()->disk('public')->directory('images/memberPhotos'),
                    DatePicker::make('dob')->required()->label('Date of Birth'),
                    TextInput::make('nid')->required()->label('NID Number'),
                    Select::make('gender')->required()->options([
                        'Male' => 'Male',
                        'Female' => 'Female',
                        'Others' => 'Others'
                    ]),
                    Select::make('religion')->required()->options([
                        'Islam' => 'Islam',
                        'Hinduism' => 'Hinduism',
                        'Buddhism' => 'Buddhism',
                        'Christianity' => 'Christianity',
                        'Others' => 'Others'
                    ]),
                    TextInput::make('age')->required()->numeric(),
                    TextInput::make('mobile')->required()->numeric(),
                ])->columnSpan(1),

            ])->columns(3);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('nid'),
                TextColumn::make('fatherName'),
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
