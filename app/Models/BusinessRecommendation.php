<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_address',
        'customer_name',
        'customer_address',
        'business_phone',
        'website',
        'detail',
        'country',
        'continent',
        'state',
        'latitude',
        'longitude',
        'user_latitude',
        'user_longitude',
        'admin_id',
        'status',
    ];

    public function getCreatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['created_at']));
    }
}
