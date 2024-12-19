<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use App\ModelFilters\BusinessFilter;
use Carbon\Carbon;
use DB, Auth;

class Business extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'country',
        'name',
        'email',
        'address',
        'category',
        'buss_phone_number',
        'buss_phone_code',
        'buss_hours',
        'detail',
        'website',
        'owner_first_name',
        'owner_last_name',
        'identification',
        'owner_phone_number',
        'owner_phone_code',
        'hiring_for_buss',
        'image',
        'national_id',
        'business_registration',
        'latitude',
        'longitude',
        'price',
        'continent',
        'state',
        'city',
        'status',
        'user_id',
        'registration_expire_date',
        'logo',
        'type',
    ];

    protected $appends = ['category_name', 'image', 'rating_avg', 'rating_count', 'collection_status', 'authentication', 'calculate_distance', 'min_price', 'max_price'];

    public function modelFilter()
    {
        return $this->provideFilter(BusinessFilter::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessImage::class, 'business_id', 'id')->select(['id', 'business_id', 'image', 'is_cover']);
    }

    public function hirings(): HasMany
    {
        return $this->hasMany(BusinessHiring::class, 'business_id', 'id')->select(['id', 'business_id', 'job_title', 'requirement', 'amount']);
    }

    public function languages(): HasMany
    {
        return $this->hasMany(BusinessLanguage::class, 'business_id', 'id')->select(['id', 'business_id', 'language_id']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'business_id', 'id')->select(['id', 'user_id', 'business_id', 'comment', 'rating'])->with(['files', 'replies']);
    }

    public function times(): HasMany
    {
        return $this->hasMany(BusinessTime::class, 'business_id', 'id')->select(['id', 'business_id', 'day', 'start_time', 'end_time', 'status']);
    }

    public function services(): HasMany
    {
        // return $this->hasMany(BusinessService::class, 'business_id', 'id')->select(['id', 'business_id', 'name']);
        return $this->hasMany(BusinessSubCategory::class, 'business_id', 'id')->select(['id', 'business_id', 'sub_category_id']);
    }

    public function options(): HasMany
    {
        return $this->hasMany(BusinessOption::class, 'business_id', 'id')->select(['id', 'business_id', 'option_id']);
    }

    public function conversationMembers(): HasMany
    {
        return $this->hasMany(BusinessConversationMember::class, 'business_id', 'id')->select(['id', 'business_id', 'user_id']);
    }

    public function conversation(): HasMany
    {
        return $this->hasMany(BusinessConversation::class, 'business_id', 'id')->select(['id', 'business_id', 'user_id', 'message', 'message_type', 'activity', 'created_at']);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'business_id', 'id');
    }

    public function keyWords(): HasMany
    {
        return $this->hasMany(BusinessKeyword::class, 'business_id', 'id')->select(['id', 'business_id', 'keyword']);
    }

    public function socialAccount(): HasMany
    {
        return $this->hasMany(BusinessSocialAccount::class, 'business_id', 'id')->select(['id', 'business_id', 'type', 'url']);
    }
    
    public function paymentOption(): HasMany
    {
        return $this->hasMany(BusinessPaymentOption::class, 'business_id', 'id')->select(['id', 'business_id', 'type', 'url']);
    }

    public function video(): HasOne
    {
        return $this->hasOne(BusinessVideo::class, 'business_id', 'id')->select(['id', 'business_id', 'type', 'video']);
    }

    public function priceMenu(): HasMany
    {
        return $this->hasMany(BusinessPriceMenu::class, 'business_id', 'id')->select(['id', 'business_id', 'menu', 'price']);
    }

    public function dealOffers(): HasMany
    {
        return $this->hasMany(DealOffer::class, 'business_id', 'id')->with(['locations']);
    }

    public function createVideo($video)
    {
        if (!empty($video['video'])) {
            $this->video()->updateOrCreate(['id' => @$video['id']], ['type' => $video['type'],'video' => $video['video']]);
        }
    }

    public function createOptions($options)
    {
        if (isset($options) && count($options) > 0) {
            $this->options()->delete();
            foreach ($options as $option) {
                $this->options()->create(['option_id' => $option]);
            }
        }
    }

    public function createKeyWords($keywords)
    {
        if (isset($keywords) && count($keywords) > 0) {
            $this->keyWords()->delete();
            foreach ($keywords as $keyword) {
                $this->keyWords()->create(['keyword' => $keyword]);
            }
        }
    }

    public function createServices($services)
    {
        if (isset($services) && count($services) > 0) {
            $this->services()->delete();
            foreach ($services as $service) {
                $this->services()->create(['sub_category_id' => $service]);
            }
        }
    }

    public function createTime($times)
    {
        if (isset($times) && count($times) > 0) {
            $this->times()->delete();
            foreach ($times as $day => $time) {
                $time['day'] = $day;
                if (isset($time['start_type'])) {
                    $time['start_time'] = $time['start_time'].' '.$time['start_type'];
                }
                if (isset($time['end_type'])) {
                    $time['end_time'] = $time['end_time'].' '.$time['end_type'];
                }
                isset($time['status']) ? $time['status'] : '0';
                $this->times()->create($time);
            }
        }
    }

    public function createBusinessImages($images)
    {
        if (!empty($images) && count($images) > 0) {
            $this->images()->delete();
            foreach ($images as $image) {
                $image = uploadFile($image, 'public/business');
                $this->images()->create(['image' => $image]);
            }
        }
    }

    public function businessImages($images)
    {
        if (!empty($images) && count($images) > 0) {
            $this->images()->delete();
            foreach ($images as $image) {
                $this->images()->create(['image' => $image]);
            }
        }
    }

    public function createHiring($hiring)
    {
        if ($this->hiring_for_buss == 'Yes' && !empty($hiring) && count($hiring) > 0) {
            $this->hirings()->delete();
            foreach ($hiring as $item) {
                $item['business_id'] = $this->id;
                $this->hirings()->create($item);
            }
        }
    }

    public function createLanguages($languageIds)
    {
        if (!empty($languageIds) && count($languageIds) > 0) {
            $this->languages()->delete();
            foreach ($languageIds as $languageId) {
                $this->languages()->create(['language_id' => $languageId]);
            }
        }
    }

    public function createSocialAccount($socials)
    {
        if (!empty($socials) && count($socials) > 0) {
            $this->socialAccount()->delete();
            foreach ($socials as $social) {
                if (!empty($social['type']) && !empty($social['url'])) {
                    $this->socialAccount()->create(['type' => $social['type'], 'url' => $social['url']]);
                }
            }
        }
    }
    
    public function createPaymentOption($socials)
    {
        if (!empty($socials) && count($socials) > 0) {
            $this->paymentOption()->delete();
            foreach ($socials as $social) {
                if (!empty($social['type']) && !empty($social['url'])) {
                    $this->paymentOption()->create(['type' => $social['type'], 'url' => $social['url']]);
                }
            }
        }
    }

    public function createPriceMenu($menus)
    {
        if (!empty($menus) && count($menus) > 0) {
            $this->priceMenu()->delete();
            foreach ($menus as $menu) {
                $this->priceMenu()->create($menu);
            }
        }
    }

    public function getCategoryNameAttribute()
    {
        return Category::where('id', @$this->attributes['category'])->pluck('name')->first();
    }

    public function getHiringForBussAttribute()
    {
        return $this->attributes['hiring_for_buss'] == '1' ? 'Yes' : 'No';
    }

    public function getImageAttribute()
    {
        return $this->images()->where('is_cover', 1)->pluck('image')->first() ?? $this->images()->pluck('image')->first();
    }

    public function getNationalIdAttribute()
    {
        // return asset('storage/' . $this->attributes['national_id']);
        return $this->attributes['national_id'] ? url('storage/') . '/' . $this->attributes['national_id'] : '';
    }

    public function getBusinessRegistrationAttribute()
    {
        return $this->attributes['business_registration'] ? url('storage/') . '/' . $this->attributes['business_registration'] : '';
    }

    public function getLogoAttribute()
    {
        return $this->attributes['logo'] ? url('storage/') . '/' . $this->attributes['logo'] : '';
    }

    public function getRatingAvgAttribute()
    {
        return round($this->reviews()->avg('rating'), 1);
    }

    public function getRatingCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function getLatitudeAttribute()
    {
        return @floatval($this->attributes['latitude']);
    }

    public function getLongitudeAttribute()
    {
        return @floatval($this->attributes['longitude']);
    }

    public function getCreatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['created_at']));
    }

    public function businessCommunity()
    {
        return [
            'members_image' => $this->conversationMembers->take(3) ?? '',
            'members_count' => max(0, $this->conversationMembers->count() - 3) ?? '',
        ];
    }

    public function getCollectionStatusAttribute()
    {
        $collectionId = @UserCollection::where('business_id', $this->attributes['id'])->pluck('collection_id')->first();
        return Collection::where(['id' => $collectionId])->pluck('name')->first();
    }

    public function getAuthenticationAttribute()
    {
        if (empty($this->attributes['national_id']) && empty($this->attributes['business_registration'])) {
            return 'unverified';
        } elseif ($this->reviews()->count() >= 25) {
            return 'verified';
        } elseif ($this->reviews()->count() < 25) {
            return 'registered';
        }
    }

    public function scopeDistance($query, $lat, $long, $radius)
    {
        return $query->select(getDistance($lat, $long))->having('distance', '<=', $radius)->orderBy('distance', 'asc');
    }

    public function getCalculateDistanceAttribute()
    {
        $user = Auth::user();
        if (!empty($user->latitude) && !empty($user->longitude)) {
            $latFrom = deg2rad($user->latitude);
            $lonFrom = deg2rad($user->longitude);
            $latTo = deg2rad($this->latitude);
            $lonTo = deg2rad($this->longitude);
            $latDiff = $latTo - $latFrom;
            $lonDiff = $lonTo - $lonFrom;
            $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($latFrom) * cos($latTo) * sin($lonDiff / 2) * sin($lonDiff / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            // Multiply by 6371 to get distance in kilometers, multiply by 1000 to get distance in meters
            return $user->business_distance == 'Kilometer' ? 6371 * $c : 6371 * $c * 0.621371;
        } else {
            return '';
        }
    }

    public function getMinPriceAttribute()
    {
        return $this->priceMenu()->min('price');
    }

    public function getMaxPriceAttribute()
    {
        return $this->priceMenu()->max('price');
    }
}
