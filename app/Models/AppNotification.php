<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'name'];
    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        return UserNotification::where(['user_id' => Auth::id(), 'notification_id' => $this->attributes['id']])->exists();
    }
}
