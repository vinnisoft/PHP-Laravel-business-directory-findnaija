<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Story extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file', 'type', 'status'];
    protected $appends = ['is_read'];

    public function getFileAttribute()
    {
        return asset('storage/'. $this->attributes['file']);
    }

    public function getIsReadAttribute()
    {
        return ViewStory::where(['story_id' => $this->attributes['id'], 'user_id' => Auth::id()])->exists();
    }
}
