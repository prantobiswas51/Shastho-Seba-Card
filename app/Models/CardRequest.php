<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardRequest extends Model
{
    protected $fillable = [
        'admin_id',
        'number',
        'type',
        'request_status',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

}
