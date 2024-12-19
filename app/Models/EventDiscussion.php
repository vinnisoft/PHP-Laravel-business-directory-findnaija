<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDiscussion extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'user_id', 'message'];

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
