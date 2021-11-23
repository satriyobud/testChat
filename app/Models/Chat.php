<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chat';
    protected $fillable = ['created_at', 'updated_at'];

    

    public function to()
    {
        return $this->hasOne(ChatMessage::class,'chat_id','id');
    }
    public function messages()
    {
        return $this->hasMany(ChatMessage::class,'chat_id','id');
    }
}
