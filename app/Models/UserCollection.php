<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCollection extends Model
{
    use HasFactory;

    protected $fillable = ['collection_id', 'business_id'];
    protected $appends = ['business_name', 'business_image', 'business_address', 'rating_avg'];

    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'business_id')->select(['id', 'name', 'address']);
    }

    public function getBusinessNameAttribute()
    {
        return $this->business()->pluck('name')->first();
    }

    public function getBusinessImageAttribute()
    {
        return BusinessImage::where('business_id', $this->attributes['business_id'])->pluck('image')->first();
    }

    public function getBusinessAddressAttribute()
    {
        return $this->business()->pluck('address')->first();
    }

    public function getRatingAvgAttribute()
    {
        return round(Review::where('business_id', $this->attributes['business_id'])->avg('rating'), 1);
    }
}
