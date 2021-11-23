<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    use HasFactory;
    protected $table = 'chat_member';
    protected $fillable = ['user_id', 'chat_id', 'email', 'name', 'created_at', 'updated_at'];

    

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }
}
