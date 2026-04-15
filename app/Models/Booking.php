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
        'start_datetime', // 🔥 NEW
        'end_datetime',   // 🔥 NEW
        'guests',
        'videoke',
        'amount',
        'paid',
        'status',
    ];

    // 🔥 OPTIONAL (RECOMMENDED)
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'videoke' => 'boolean',
        'amount' => 'decimal:2',
        'paid' => 'decimal:2',
    ];
}