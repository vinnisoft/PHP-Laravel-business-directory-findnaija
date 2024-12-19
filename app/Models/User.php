<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'country',
        'phone_number',
        'dob',
        'login_type',
        'social_id',
        'otp',
        'latitude',
        'longitude',
        'profile_image',
        'fcm_token',
        'business_distance',
        'email_verified_at',
    ];

    protected $appends = ['check_in_status'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function subscription(): HasOne
    {
        return $this->hasOne(UserSubscription::class, 'user_id', 'id')->select(['id', 'user_id', 'plan_id', 'start_date', 'end_date'])->orderBy('id', 'DESC');
    }

    public function getDobAttribute()
    {
        return date('d M Y', strtotime($this->attributes['dob']));
    }
    
    public function getCreatedAtAttribute()
    {
        return date('d M Y', strtotime($this->attributes['created_at']));
    }

    public function getProfileImageAttribute()
    {
        return $this->attributes['profile_image'] ? url('storage/').'/'.$this->attributes['profile_image'] : '';
    }

    public function routeNotificationForFcm ($notifiable) {
        return 'identifier-from-notification-for-fcm: ' . $this->id;
    }

    public function scopeLocatedWithinRadius(Builder $query, $lat, $long, $radius)
    {
        return $query->select('*', DB::raw('( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->whereRaw('( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) <= ?', [
                $lat, $long, $lat, $lat, $long, $lat, $radius
            ])->orderBy('distance');
    }

    public function getCheckInStatusAttribute()
    {
        return BusinessConversationMember::where('user_id', $this->attributes['id'])->exists();
    }
}
