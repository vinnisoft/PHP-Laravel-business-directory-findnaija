<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use EloquentFilter\Filterable;
use App\ModelFilters\EventFilter;
use DB, Auth;

class Event extends Model
{
    use HasFactory, Filterable;

    protected $fillable = ['business_id', 'title', 'image', 'address', 'latitude', 'longitude', 'scope', 'category_id', 'description', 'start_date', 'end_date', 'start_time', 'end_time'];

    protected $appends = ['business_name', 'interested', 'interest_count', 'going', 'going_count', 'category_name', 'event_on', 'user_id'];

    public function modelFilter()
    {
        return $this->provideFilter(EventFilter::class);
    }

    public function eventMember(): HasMany
    {
        return $this->hasMany(EventMember::class, 'event_id', 'id')->select(['id', 'user_id', 'event_id'])->orderBy('id', 'DESC');
    }

    public function goingToEvent(): HasMany
    {
        return $this->hasMany(GoingToEvent::class, 'event_id', 'id')->orderBy('id', 'DESC');
    }

    public function getImageAttribute()
    {
        return url('storage/').'/'.$this->attributes['image'];
    }

    public function getBusinessNameAttribute()
    {
        return getBusinessNameById($this->attributes['business_id']);
    }

    public function getCategoryNameAttribute()
    {
        return EventCategory::where('id', $this->attributes['category_id'])->pluck('name')->first();
    }

    public function getStartDateAttribute()
    {
        return date('Y-m-d', strtotime($this->attributes['start_date']));
    }

    public function getEndDateAttribute()
    {
        return date('Y-m-d', strtotime($this->attributes['end_date']));
    }

    public function getInterestedAttribute()
    {
        return $this->eventMember()->where('user_id', Auth::id())->exists();
    }

    public function getGoingAttribute()
    {
        return $this->goingToEvent()->where('user_id', Auth::id())->exists();
    }

    public function getInterestCountAttribute()
    {
        return $this->eventMember()->count();
    }

    public function getGoingCountAttribute()
    {
        return $this->goingToEvent()->count();
    }

    public function getEventOnAttribute()
    {
        return date('D', strtotime($this->attributes['start_date'])).', '.date('d M', strtotime($this->attributes['start_date'])).' AT '.date('H:i', strtotime($this->attributes['start_time']));
    }

    public function getUserIdAttribute()
    {
        return Business::where('id', $this->attributes['business_id'])->pluck('user_id')->first();
    }

    public function scopeLocatedWithinRadius(Builder $query, $lat, $long, $radius)
    {
        return $query->select('*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [$lat, $long, $lat])
            ->whereRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?', [$lat, $long, $lat, $radius])
            ->orderBy('distance');
    }
}
