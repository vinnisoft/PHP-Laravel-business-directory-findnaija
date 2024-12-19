<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessImage extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'image', 'is_cover'];

    public function getImageAttribute()
    {
        return $this->attributes['image'] ? url('storage/').'/'.$this->attributes['image'] : '';
    }
}
