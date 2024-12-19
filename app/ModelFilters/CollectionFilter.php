<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use App\Models\User;
use App\Models\Business;
use Auth, DB;

class CollectionFilter extends ModelFilter
{    
    public $relations = [];

    public function past($past)
    {
        if (isset($past) && $past == 1) {
            return $this->whereDate('start_date', '<', date('Y-m-d'));
        }        
    }
    
    public function search($search)
    {
        switch ($search) {
            case 'near_by':
                $user = Auth::user();                
                $userIds = User::locatedWithinRadius($user->latitude, $user->latitude, 50)->pluck('id');
                return $this->whereIn('user_id', $userIds)->where('featured', '1');        
            break;
            case 'all':
                return $this->whereIn('user_id', User::pluck('id'));
            break;
            case 'my':
                return $this->where('user_id', Auth::id());
            break;       
        }
    }

    public function city($city)
    {
        $result = \Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'address' => $city,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);
        $coordinates = $result['results'][0]['geometry']['location'];
        $userIds = User::locatedWithinRadius($coordinates['lat'], $coordinates['lng'], 50)->pluck('id');
        return $this->whereIn('user_id', $userIds)->where('featured', '1');      
    }
}
