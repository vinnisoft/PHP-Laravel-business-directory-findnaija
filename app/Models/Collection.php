<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use EloquentFilter\Filterable;
use App\ModelFilters\CollectionFilter;
use Auth;

class Collection extends Model
{
    use HasFactory, Filterable;

    protected $fillable = ['user_id', 'name', 'picture', 'description', 'featured', 'scope'];

    protected $appends = ['collection_count'];

    public function modelFilter()
    {
        return $this->provideFilter(CollectionFilter::class);
    }

    public function userCollection(): HasMany
    {
        switch (request()->sort) {
            case 'near_by':
                $user = Auth::user();
                $businessIds = Business::distance($user->latitude, $user->longitude, 50)->pluck('id');
                return $this->hasMany(UserCollection::class, 'collection_id', 'id')->whereIn('business_id', $businessIds)->select(['id', 'collection_id', 'business_id'])->orderBy('id', 'DESC');
            break;
            case 'all':
                $businessIds = Business::where('status', '1')->pluck('id');
                return $this->hasMany(UserCollection::class, 'collection_id', 'id')->whereIn('business_id', $businessIds)->select(['id', 'collection_id', 'business_id'])->orderBy('id', 'DESC');
            break;
            default:
                return $this->hasMany(UserCollection::class, 'collection_id', 'id')->select(['id', 'collection_id', 'business_id'])->orderBy('id', 'DESC');
            break;
        }
    }

    public function getCollectionCountAttribute()
    {
        return $this->userCollection()->count();
    }

    public function getPictureAttribute()
    {
        $businessIds = $this->userCollection->pluck('business_id')->take(2)->toArray();

        if (!empty($businessIds)) {
            $images = [];
            foreach ($businessIds as $businessId) {
                $getImage = BusinessImage::where('business_id', $businessId)->orderBy('id', 'ASC')->pluck('image')->first();
                $images[] = $getImage;
            }
            return $images;
        }

        return [];
    }
}
