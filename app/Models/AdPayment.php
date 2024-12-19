<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdPayment extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'user_id', 'price', 'payment_method'];
}
