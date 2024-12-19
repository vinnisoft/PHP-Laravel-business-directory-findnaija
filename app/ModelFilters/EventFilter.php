<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use App\Models\User;
use App\Models\Business;
use Auth, DB;

class EventFilter extends ModelFilter
{    
    public $relations = [];

    public function past($past)
    {
        if (isset($past) && $past == 1) {
            return $this->whereDate('start_date', '<', date('Y-m-d'));
        }        
    }
    
    public function sort($sort)
    {
        switch ($sort) {
            case 'near_by':
                $user = Auth::user();
                return $this->select('*')
                    ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$user->latitude,  $user->longitude, $user->latitude])
                    ->whereRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?', [$user->latitude,  $user->longitude, $user->latitude, 50])
                    ->orderBy('distance');
            break;   
            break;
            case 'all':
                $businessIds = Business::where('status', '!=', 'rejected')->pluck('id');
                return $this->whereIn('business_id', $businessIds);
            break;
            case 'my':
                $businessIds = Business::where('user_id', Auth::id())->where('status', '!=', 'rejected')->pluck('id');
                return $this->whereIn('business_id', $businessIds);
            break;         
        }
    }
}
