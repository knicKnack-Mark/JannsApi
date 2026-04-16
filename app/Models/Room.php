<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
    'name',
    'price',
    'max_pax',
    'image',
    'available'
];
}
