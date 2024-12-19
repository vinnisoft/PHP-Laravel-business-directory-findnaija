<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealOfferLocation extends Model
{
    use HasFactory;

    protected $fillable = ['deal_offer_id', 'location', 'latitude', 'longitude'];
}
