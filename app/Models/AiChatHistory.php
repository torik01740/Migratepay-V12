<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'sender_type',
        'message',
        'visitor_name',
        'visitor_contact',
    ];
}
