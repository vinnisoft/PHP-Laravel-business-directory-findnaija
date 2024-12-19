<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityFeed extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'data'];    

    public function getDataAttribute($value)
    {
        return json_decode($value);
    }
}
