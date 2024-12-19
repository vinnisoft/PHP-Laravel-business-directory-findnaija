<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BusinessHiring extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'job_title', 'requirement', 'amount'];

    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'id', 'business_id')->select(['id', 'name', 'email', 'buss_phone_number', 'website']);
    }
}
