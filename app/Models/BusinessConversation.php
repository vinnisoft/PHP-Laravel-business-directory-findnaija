<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'user_id',
        'message',
        'message_type',
        'activity',
    ];

    protected $appends = ['user_name', 'user_image'];

    public function getUserNameAttribute()
    {
        return getUserNameById($this->attributes['user_id']);
    }

    public function getUserImageAttribute()
    {
        return getUserImageById($this->attributes['user_id']);
    }
}
