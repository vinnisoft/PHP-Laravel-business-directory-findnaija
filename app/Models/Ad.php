<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'caption',
        'btn_title',
        'btn_link',
        'type',
        'business_id',
        'ad_goal_id',
        'status',
        'start_date',
        'end_date',
        'budget',
        'is_active',
    ];

    public function media(): HasOne
    {
        return $this->hasOne(AdMedia::class, 'ad_id', 'id')->select('id', 'ad_id', 'media');
    }

    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'business_id');
    }

    public function goal(): HasOne
    {
        return $this->hasOne(AdGoal::class, 'id', 'ad_goal_id')->select('id', 'title', 'description');
    }

    public function audiance(): HasMany
    {
        return $this->hasMany(AdAudience::class, 'ad_id', 'id');
    }

    public function location(): HasMany
    {
        return $this->hasMany(AdLocation::class, 'ad_id', 'id');
    }
}
