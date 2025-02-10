<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($member) {
            if ($member->card_id) {
                $card = \App\Models\Card::find($member->card_id);
                $admin = \App\Models\User::find(auth()->id());

                if ($card && $admin && $admin->balance >= $card->price) {
                    // Deduct balance and activate card
                    $card->update(['status' => 'Active']);
                    $admin->decrement('balance', $card->price);
                } else {
                    // Remove assigned card if balance is insufficient
                    $member->update(['card_id' => null]);

                    Notification::make()
                        ->title('Low Balance')
                        ->body('Low balance, please recharge.')
                        ->danger()
                        ->send();
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'fatherName',
        'motherName',
        'dob',
        'nid',
        'gender',
        'religion',
        'mobile',
        'member_photo',
        'age',
        'address',
        'family_members',

        'district_id',
        'sub_district_id',
        'card_id',
        'admin_id',
    ];

    protected $casts = [
        'family_members' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function subdistrict()
    {
        return $this->belongsTo(Subdistrict::class);
    }

    public function sub_district()
    {
        return $this->belongsTo(SubDistrict::class);
    }
}
