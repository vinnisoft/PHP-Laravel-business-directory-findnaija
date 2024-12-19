<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use App\Models\BusinessLanguage;
use App\Models\Review;
use App\Models\BusinessSubCategory;

class BusinessFilter extends ModelFilter
{    
    public $relations = [];

    public function search($search)
    {
        $type = array_keys($search)[0];
        switch ($type) {
            case 'name':
                return $this->where('name', 'LIKE', "%$search[$type]%")
                ->orWhereHas('keyWords', function ($query) use ($search) {
                    $query->where('keyword', 'LIKE', "%$search[name]%");
                });;
            break;
            case 'location':
                return $this->where('continent', 'LIKE', "%$search[$type]%")
                    ->orWhere('address', 'LIKE', "%$search[$type]%")
                    ->orWhere('country', 'LIKE', "%$search[$type]%")
                    ->orWhere('state', 'LIKE', "%$search[$type]%");
            break;
            case 'rating':
                return $this->rating($search[$type]);
            break;
        }
    }

    public function categoryId($categoryId)
    {               
        return $this->where('category', $categoryId);
    }

    public function option($option)
    {
        $businessIds = BusinessSubCategory::where('sub_category_id', $option)->pluck('business_id')->toArray();
        return $this->whereIn('id', $businessIds);
    }

    public function continent($continent)
    {
        return $this->where('continent', $continent);
    }

    public function country($country)
    {
        return $this->where('country', $country);
    }

    public function state($state)
    {
        return $this->where('state', $state);
    }

    public function city($city)
    {
        return $this->where('city', $city);
    }

    public function hiring($hiring)
    {
        return $this->where('hiring_for_buss', $hiring);
    }

    public function languageId($languageId)
    {
        $bussIds = BusinessLanguage::whereIn('language_id', $languageId)->pluck('business_id');       
        return $this->whereIn('id', $bussIds);
    }

    public function price($price)
    {               
        return $this->whereBetween('price', [$price['start_price'], $price['end_price']]);
    }
    
    public function rating($rating)
    {               
        $bussIds = Review::where('rating', $rating)->pluck('business_id')->toArray();       
        return $this->whereIn('id', $bussIds);
    }    
}
