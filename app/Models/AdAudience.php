<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdAudience extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['category_name'];

    public function getCategoryNameAttribute()
    {
        return Category::where('id', $this->attributes['category_id'])->pluck('name')->first();
    }
}
