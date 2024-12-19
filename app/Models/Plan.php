<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['price', 'name', 'saving', 'description', 'type'];

    protected $appends = ['is_active'];    

    public function getIsActiveAttribute()
    {
        return UserSubscription::where(['plan_id' => $this->attributes['id'], 'user_id' => Auth::id(), 'status' => 'current'])->exists();
    }
}
