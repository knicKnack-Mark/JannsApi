<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    protected $fillable = [
        'staff_id',
        'attendance_date',
        'time_in',
        'time_out',
        'rendered_hours',
        'status',
        'remarks',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}