<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessConversationMember extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'business_id', 'online', 'unread_messages'];

    protected $appends = ['user_image'];

    public function getUserImageAttribute()
    {
        return getUserImageById($this->attributes['user_id']);
    }
}
