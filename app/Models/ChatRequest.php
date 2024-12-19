<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRequest extends Model
{
    use HasFactory;

    protected $fillable = ['requested_by', 'requested_to', 'status', 'delete_requested_by', 'delete_requested_to', 'deleted_msg_date_by', 'deleted_msg_date_to'];
}
