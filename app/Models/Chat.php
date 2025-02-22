<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;


    protected $fillable = ['type','name', 'created_by','group_avatar'];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(group_members::class, 'chat_id');
    }

    public function group_members()
    {
        return $this->hasMany(group_members::class, 'chat_id');
    }


    public function latestMessage()
{
    return $this->hasOne(Message::class, 'chat_id')->latest();
}

public function messages()
{
    return $this->hasMany(Message::class, 'chat_id');
}

}
