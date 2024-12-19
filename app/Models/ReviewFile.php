<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewFile extends Model
{
    use HasFactory;

    protected $fillable = ['review_id', 'file'];

    public function getFileAttribute()
    {
        return $this->attributes['file'] ? url('storage/').'/'.$this->attributes['file'] : '';
    }
}
