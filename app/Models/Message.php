<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_id','sender_id', 'receiver_id', 'content', 'status', 'message_type'];

  
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Crypt::encryptString($value);
    }

    
    public function getContentAttribute($value)
    {
        return Crypt::decryptString($value);
    }


    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the recipient of the message.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

     /**
     * Get the chat of the message.
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
    public function messageFiles()
    {
        return $this->hasMany(MessageFile::class, 'message_id');
    }
    

}
