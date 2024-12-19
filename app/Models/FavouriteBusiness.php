<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FavouriteBusiness extends Model
{
    use HasFactory;

    protected $table = 'favorite_businesses';

    protected $fillable = ['business_id', 'description'];

    protected $appends = ['business_name', 'business_logo', 'business_location', 'photo'];

    public function photos(): HasMany
    {
        return $this->hasMany(FavouriteBusinessPhotos::class, 'favorite_business_id', 'id')->select(['id', 'favorite_business_id', 'photo']);
    }

    public function getBusinessNameAttribute()
    {
        return getBusinessNameById($this->attributes['business_id']);
    }

    public function getBusinessLogoAttribute()
    {
        return Business::where('id', $this->attributes['business_id'])->pluck('logo')->first();
    }

    public function getBusinessLocationAttribute()
    {
        return Business::where('id', $this->attributes['business_id'])->pluck('address')->first();
    }

    public function getPhotoAttribute()
    {
        return $this->photos()->pluck('photo')->first();
    }
}
