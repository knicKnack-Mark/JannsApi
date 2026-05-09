<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'booking_id',
        'guest_name'
    ];

    /* =========================
       RELATIONSHIP
    ========================= */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}