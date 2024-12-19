<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReport extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'business_id', 'category', 'reason'];

    public function getCreatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['created_at']));
    }
}
