<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'name', 'icon', 'show_on_home', 'graphic_image', 'graphic_on_home', 'category_on_home'];

    protected $appends = ['icon_path'];

    public function categoryGroup(): HasOne
    {
        return $this->hasOne(CategoryGroup::class, 'id', 'group_id')->select(['id', 'name']);
    }

    public function getIconAttribute()
    {
        return url('storage/').'/'.$this->attributes['icon'];
    }

    public function getIconPathAttribute()
    {
        return $this->attributes['icon'];
    }

    public function group()
    {
        return $this->belongsTo(CategoryGroup::class, 'group_id');
    }

    public function getGraphicImageAttribute($value)
    {
        return $value ? asset('storage/'. $value) : NULL;
    }
}
