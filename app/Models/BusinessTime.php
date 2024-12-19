<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTime extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'day', 'start_time', 'end_time', 'status'];
}
