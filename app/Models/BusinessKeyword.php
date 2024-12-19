<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessKeyword extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'keyword'];
}
