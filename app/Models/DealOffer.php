<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'title',
        'picture',
        'description',
        'start_date',
        'end_date',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(DealOfferLocation::class, 'deal_offer_id', 'id')->select(['id', 'deal_offer_id', 'location', 'latitude', 'longitude']);
    }

    public function getPictureAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
