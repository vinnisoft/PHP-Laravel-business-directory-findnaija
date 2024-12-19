<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdMedia extends Model
{
    use HasFactory;

    protected $fillable = ['ad_id', 'media'];

    public function getMediaAttribute()
    {
        return asset('storage/'.$this->attributes['media']);
    }
}
