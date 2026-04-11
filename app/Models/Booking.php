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
        'date',
        'time',
        'guests',
        'videoke',
        'amount',
        'paid',
    ];
}