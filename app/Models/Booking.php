<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'cabin',
        'start_datetime',
        'end_datetime',
        'guests',

        // NEW
        'max_pax',
        'extra_pax',
        'extra_pax_rate',
        'extra_pax_discount',
        'extra_pax_total',

        'videoke',
        'amount',
        'paid',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime:Y-m-d H:i:s',
        'end_datetime' => 'datetime:Y-m-d H:i:s',

        'videoke' => 'boolean',

        'amount' => 'decimal:2',
        'paid' => 'decimal:2',

        // NEW
        'extra_pax_rate' => 'decimal:2',
        'extra_pax_discount' => 'decimal:2',
        'extra_pax_total' => 'decimal:2',
    ];

    public function attendance()
    {
        return $this->hasMany(
            \App\Models\Attendance::class
        );
    }
}