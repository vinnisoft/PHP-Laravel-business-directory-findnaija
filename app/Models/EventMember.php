<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMember extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id'];

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
