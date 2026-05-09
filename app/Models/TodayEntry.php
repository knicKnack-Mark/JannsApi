<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TodayEntry extends Model
{
    protected $fillable = [
        'booking_id',
        'guest_name',
        'cabin',
        'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}