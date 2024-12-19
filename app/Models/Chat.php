<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = ['room_id', 'user_id', 'message', 'message_type', 'unread_messages'];
    protected $appends = ['user_name', 'msg_time'];

    public function getMessageAttribute()
    {
        return $this->attributes['message_type'] == 'text' ? $this->attributes['message'] : asset('storage/' . $this->attributes['message']);
    }

    public function getUserNameAttribute()
    {
        return getUserNameById($this->attributes['user_id']);
    }

    public function scopeBlockMsg($query, $roomId)
    {
        $blockRange = BlockRange::where(['room_id' => $roomId, 'block_by' => Auth::id()])->select('start_date', 'end_date')->get();
        $flatIds = $blockRange->flatMap(function ($range) use ($roomId) {
            $chatQuery = Chat::where('room_id', $roomId);
            if (isset ($range->end_date)) {
                $chatQuery->whereBetween('created_at', [$range->start_date, $range->end_date]);
            } else {
                $chatQuery->where('created_at', '>', $range->start_date);
            }
            return $chatQuery->pluck('id')->toArray();
        })->toArray();
        return $query->whereNotIn('id', $flatIds);
    }

    public function getMsgTimeAttribute()
    {
        return date('H:i', strtotime($this->attributes['created_at']));
    }
}