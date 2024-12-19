<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessLanguage extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'language_id'];

    protected $appends = ['language'];

    public function getLanguageAttribute()
    {
        return Language::where('id', $this->attributes['language_id'])->pluck('name')->first();
    }
}
