<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockRange extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'block_by', 'start_date', 'end_date'];
}
