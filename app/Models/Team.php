<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    protected $fillable = ['name', 'supervisor_id'];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'team_admins', 'team_id', 'admin_id');
    }
}
