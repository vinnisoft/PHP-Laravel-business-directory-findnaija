<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectBusinessReason extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'user_id', 'reason'];
}
