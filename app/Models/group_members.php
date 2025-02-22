<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class group_members extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id','user_id', 'role' ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     /**
     * Get the chat of the message.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
}
