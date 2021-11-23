<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $table = 'chat_message';
    protected $fillable = ['user_id', 'chat_id', 'message', 'created_at', 'updated_at'];

    

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
