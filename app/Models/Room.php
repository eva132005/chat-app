<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'type'];

    public function members()
    {
        return $this->belongsToMany(User::class, 'room_members');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}