<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'icon'];

    protected $appends= ['icon_name'];

    public function getIconAttribute()
    {
        return url('storage/').'/'.$this->attributes['icon'];
    }

    public function getIconNameAttribute()
    {
        return $this->attributes['icon'];
    }
}
