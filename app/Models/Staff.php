<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'name',
        'position',
        'phone',
        'salary_type',
        'monthly_salary',
        'daily_rate',
        'status',
        'attendance',
        'avatar',
    ];

        public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}