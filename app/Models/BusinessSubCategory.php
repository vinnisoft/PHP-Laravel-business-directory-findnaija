<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'sub_category_id'];

    protected $appends = ['name', 'icon'];

    public function getNameAttribute()
    {
        return SubCategory::where('id', $this->attributes['sub_category_id'])->pluck('name')->first();
    }

    public function getIconAttribute()
    {
        return SubCategory::where('id', $this->attributes['sub_category_id'])->pluck('icon')->first();
    }
}
