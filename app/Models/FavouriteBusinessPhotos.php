<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavouriteBusinessPhotos extends Model
{
    use HasFactory;

    protected $table = 'favorite_business_photos';

    protected $fillable = ['favorite_business_id', 'photo'];

    public function getPhotoAttribute($value)
    {
        return asset('storage/'.$value);
    }
}
