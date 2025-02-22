<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageFile extends Model
{
    use HasFactory;

    
    protected $table = 'message_files'; 
    
    protected $fillable = [
        'message_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    /**
     * Define the relationship: A message file belongs to a message.
     */
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
