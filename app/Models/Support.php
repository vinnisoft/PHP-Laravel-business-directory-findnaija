<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'notes',
        'business_id',
        'national_id',
        'business_registration',
    ];

    public function getNationalIdAttribute()
    {
        return $this->attributes['national_id'] ? url('storage/').'/'.$this->attributes['national_id'] : '';
    }

    public function getBusinessRegistrationAttribute()
    {
        return $this->attributes['business_registration'] ? url('storage/').'/'.$this->attributes['business_registration'] : '';
    }
}
