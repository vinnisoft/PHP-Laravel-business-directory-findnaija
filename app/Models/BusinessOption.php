<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOption extends Model
{
    use HasFactory;

    protected $fillable= ['business_id', 'option_id'];

    protected $appends = ['option_name', 'icon'];

    public function getOptionNameAttribute()
    {
        return Option::where('id', $this->attributes['option_id'])->pluck('name')->first();
    }

    public function getIconAttribute()
    {
        return Option::where('id', $this->attributes['option_id'])->pluck('icon')->first();
    }
}
