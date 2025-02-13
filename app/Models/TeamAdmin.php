<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamAdmin extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'admin_id'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
