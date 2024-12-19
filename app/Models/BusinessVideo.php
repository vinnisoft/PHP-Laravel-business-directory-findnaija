<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class BusinessVideo extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'type', 'video'];

    public function getVideoAttribute($value)
    {
        $validator = Validator::make(['url' => $value], [
            'url' => 'url',
        ]);
        
        if ($validator->passes()) {
            return $value;
        } else {
            return asset('storage/'.$value);
        }
    }
}
