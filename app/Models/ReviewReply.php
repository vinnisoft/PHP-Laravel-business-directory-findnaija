<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = ['review_id', 'user_id', 'comment'];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id', 'first_name', 'last_name', 'profile_image']);
    }
}
