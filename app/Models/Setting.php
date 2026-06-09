<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'profile',
        'system',
        'attendance',
        'payroll',
        'departments',
        'positions',
    ];

    protected $casts = [
        'profile' => 'array',
        'system' => 'array',
        'attendance' => 'array',
        'payroll' => 'array',
        'departments' => 'array',
        'positions' => 'array',
    ];
}