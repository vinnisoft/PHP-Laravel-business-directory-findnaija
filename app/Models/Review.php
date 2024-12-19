<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Auth;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'business_id', 'comment', 'rating'];

    protected $appends = ['user_name', 'user_profile', 'like', 'like_count', 'report'];

    public function files(): HasMany
    {
        return $this->hasMany(ReviewFile::class, 'review_id', 'id')->select(['id', 'review_id', 'file']);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ReviewLike::class, 'review_id', 'id')->select(['id', 'review_id', 'user_id']);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ReviewReply::class, 'review_id', 'id')->select(['id', 'review_id', 'user_id', 'comment'])->with(['user']);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class, 'review_id', 'id')->select(['id', 'review_id', 'user_id', 'reason']);
    }

    public function createFiles($files)
    {
        if (isset($files) && count($files) > 0) {
            foreach ($files as $file) {
                $this->files()->create(['file' => $file]);
            }
        }
    }

    public function getRatingAttribute()
    {
        return round($this->attributes['rating'], 1);
    }

    public function getUserNameAttribute()
    {
        return getUserNameById($this->attributes['user_id']);
    }

    public function getUserProfileAttribute()
    {
        return getUserImageById($this->attributes['user_id']);
    }

    public function getLikeAttribute()
    {
        return ReviewLike::where('review_id', $this->attributes['id'])->where('user_id', Auth::id())->exists();
    }

    public function getLikeCountAttribute()
    {
        return ReviewLike::where('review_id', $this->attributes['id'])->count();
    }

    public function getReportAttribute()
    {
        return $this->reports()->where('user_id', Auth::id())->exists();
    }
}
