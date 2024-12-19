<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'plan_id', 'start_date', 'end_date'];

    protected $appends = ['plan'];

    public function getPlanAttribute()
    {
        return Plan::where('id', $this->attributes['plan_id'])->first();
    }
}
